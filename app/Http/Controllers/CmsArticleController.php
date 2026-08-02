<?php

namespace App\Http\Controllers;

use App\Helpers\SafeUpload;
use App\Models\Article;
use App\Models\UnlistedStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CmsArticleController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', '');

        $query = Article::query()->with('author')->orderByDesc('updated_at');

        if ($status === 'trash') {
            $query->onlyTrashed();
        } elseif (in_array($status, ['draft', 'published'], true)) {
            $query->where('status', $status);
        }

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        $articles = $query->paginate(20)->withQueryString();

        return view('admin.cms.index', compact('articles', 'status', 'search'));
    }

    public function create()
    {
        $article = new Article();
        $selectedStocks = collect();

        return view('admin.cms.form', compact('article', 'selectedStocks'));
    }

    public function store(Request $request)
    {
        $data = $this->validateArticle($request);

        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['created_by'] = session('uid');

        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->storeFeaturedImage($request);
        }

        $article = Article::create($data);
        $article->unlistedStocks()->sync($request->input('stock_tickers', []));

        return redirect()->route('admin.cms.articles.edit', $article->id)
            ->with('success', 'Article created.');
    }

    // How long an editor's tab is considered "still open" without a heartbeat.
    // Keep in sync with the heartbeat interval in admin/cms/form.blade.php.
    private const LOCK_TTL_MINUTES = 3;

    // Whether $article is currently locked by someone other than $uid. A lock
    // is only "live" while its holder's tab keeps sending heartbeats within
    // LOCK_TTL_MINUTES — a closed tab or crashed browser lets it expire so the
    // article doesn't get stuck locked forever.
    private function isLockedByOther(Article $article, $uid): bool
    {
        $isLive = $article->locked_by
            && $article->locked_at
            && $article->locked_at->gt(now()->subMinutes(self::LOCK_TTL_MINUTES));

        return $isLive && (int) $article->locked_by !== (int) $uid;
    }

    private function lockInfo(Article $article): array
    {
        return [
            'name'  => $article->lockedByUser->name ?? 'Another user',
            'since' => $article->locked_at->diffForHumans(),
        ];
    }

    // Claiming the lock is metadata, not a content edit — must not bump
    // updated_at, which the sidebar shows as "Last updated".
    private function claimLock(Article $article, $uid): void
    {
        $article->timestamps = false;
        $article->forceFill(['locked_by' => $uid, 'locked_at' => now()])->save();
    }

    public function edit(int $id)
    {
        $article = Article::findOrFail($id);
        $selectedStocks = $article->unlistedStocks()->get(['unlisted_stocks.UL_STOCKS_FINCODE', 'unlisted_stocks.UL_STOCKS_COMPNAME']);

        $uid = session('uid');
        $lockedBy = null;

        // First person to open the article for editing holds the lock — everyone
        // else gets a read-only view with a "who's editing this" notice until the
        // holder finishes (saves/leaves) or their tab goes quiet past the TTL.
        if ($this->isLockedByOther($article, $uid)) {
            $lockedBy = $this->lockInfo($article);
        } else {
            $this->claimLock($article, $uid);
        }

        return view('admin.cms.form', compact('article', 'selectedStocks', 'lockedBy'));
    }

    public function heartbeat(int $id)
    {
        $article = Article::findOrFail($id);
        $uid = session('uid');

        if ($this->isLockedByOther($article, $uid)) {
            return response()->json(['locked' => true] + $this->lockInfo($article));
        }

        $this->claimLock($article, $uid);

        return response()->json(['locked' => false]);
    }

    public function releaseLock(int $id)
    {
        // Plain query-builder update (not the Eloquent model) so this doesn't
        // touch updated_at either.
        DB::table('articles')
            ->where('id', $id)
            ->where('locked_by', session('uid'))
            ->update(['locked_by' => null, 'locked_at' => null]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, int $id)
    {
        $article = Article::findOrFail($id);

        // Enforced server-side, not just via the disabled form on the locked-out
        // viewer's page — a client can always replay the raw POST.
        if ($this->isLockedByOther($article, session('uid'))) {
            $lock = $this->lockInfo($article);
            return redirect()->route('admin.cms.articles.edit', $article->id)
                ->with('lock_error', "{$lock['name']} is currently editing this article (started {$lock['since']}). Your changes were not saved.");
        }

        $data = $this->validateArticle($request, $article->id);

        // The slug/URL only changes when explicitly edited via its own field —
        // editing the title never touches it, so published links and Google's
        // index of this article stay stable.
        $submittedSlug = trim((string) $request->input('slug', ''));
        if ($submittedSlug !== '') {
            $normalizedSlug = Str::slug($submittedSlug);
            if ($normalizedSlug !== '' && $normalizedSlug !== $article->slug) {
                $data['slug'] = $this->uniqueSlug($normalizedSlug, $article->id);
            }
        }

        if ($data['status'] === 'published' && $article->status !== 'published') {
            $data['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            $this->deleteImageFile($article->featured_image);
            $data['featured_image'] = $this->storeFeaturedImage($request);
        }

        $article->update($data);
        $article->unlistedStocks()->sync($request->input('stock_tickers', []));

        return redirect()->route('admin.cms.articles.edit', $article->id)
            ->with('success', 'Article updated.');
    }

    public function publishToggle(int $id)
    {
        $article = Article::findOrFail($id);
        $newStatus = $article->status === 'published' ? 'draft' : 'published';

        $article->status = $newStatus;
        if ($newStatus === 'published' && !$article->published_at) {
            $article->published_at = now();
        }
        $article->save();

        return response()->json(['success' => true, 'status' => $newStatus]);
    }

    public function trash(int $id)
    {
        Article::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    public function restore(int $id)
    {
        Article::onlyTrashed()->findOrFail($id)->restore();

        return response()->json(['success' => true]);
    }

    public function forceDelete(int $id)
    {
        $article = Article::onlyTrashed()->findOrFail($id);
        $this->deleteImageFile($article->featured_image);
        $article->forceDelete();

        return response()->json(['success' => true]);
    }

    public function uploadContentImage(Request $request)
    {
        $request->validate(['file' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120']);

        $folder = public_path('images/article-content');
        if (!is_dir($folder)) mkdir($folder, 0755, true);

        $file = $request->file('file');
        $ext  = SafeUpload::imageExtension($file);
        abort_if($ext === null, 422, 'Unsupported image type.');
        $filename = 'article_' . time() . '_' . uniqid() . '.' . $ext;
        $file->move($folder, $filename);

        return response()->json(['location' => asset('images/article-content/' . $filename)]);
    }

    public function searchStocks(Request $request)
    {
        $term = trim((string) $request->query('term', ''));

        $stocks = DB::table('unlisted_stocks')
            ->where('UL_STOCKS_STATUS', '1')
            ->when($term !== '', fn($q) => $q->where('UL_STOCKS_COMPNAME', 'like', '%' . $term . '%'))
            ->orderBy('UL_STOCKS_COMPNAME')
            ->limit(20)
            ->get(['UL_STOCKS_FINCODE as fincode', 'UL_STOCKS_COMPNAME as name']);

        return response()->json($stocks);
    }

    private function validateArticle(Request $request, ?int $ignoreId = null): array
    {
        $isCreate = $ignoreId === null;

        $data = $request->validate([
            'title'                => [
                'required', 'string', 'max:255',
                \Illuminate\Validation\Rule::unique('articles', 'title')->ignore($ignoreId),
            ],
            'summary'              => 'required|string|max:1000',
            'content'              => 'required|string',
            'status'               => 'required|in:draft,published',
            'featured_image'       => [$isCreate ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'meta_title'           => 'required|string|max:255',
            'meta_description'     => 'required|string|max:500',
            'meta_keywords'        => 'required|string|max:500',
            'stock_tickers'        => 'nullable|array',
            'stock_tickers.*'      => 'exists:unlisted_stocks,UL_STOCKS_FINCODE',
        ], [
            'title.unique' => 'An article with this exact title already exists.',
        ]);

        // The editor produces rich HTML, but it's attacker-controllable (any cms.author/
        // cms.reviewer account) and rendered raw on the public article page — strip it
        // down to a safe tag/attribute allow-list so it can't carry script payloads.
        $data['content'] = clean($data['content']);

        // These fields are meant to be plain text (page <title>/<meta> content); they must
        // never carry markup since the layout renders them without further escaping context.
        $data['meta_title']       = strip_tags($data['meta_title']);
        $data['meta_description'] = strip_tags($data['meta_description']);
        $data['meta_keywords']    = strip_tags($data['meta_keywords']);

        return $data;
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Article::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    private function storeFeaturedImage(Request $request): string
    {
        $folder = public_path('images/articles');
        if (!is_dir($folder)) mkdir($folder, 0755, true);

        $file = $request->file('featured_image');
        $ext  = SafeUpload::imageExtension($file);
        abort_if($ext === null, 422, 'Unsupported image type.');
        $filename = 'article_' . time() . '_' . uniqid() . '.' . $ext;
        $file->move($folder, $filename);

        return 'images/articles/' . $filename;
    }

    private function deleteImageFile(?string $relativePath): void
    {
        if (!$relativePath) return;

        $fullPath = public_path($relativePath);
        if (is_file($fullPath)) @unlink($fullPath);
    }
}
