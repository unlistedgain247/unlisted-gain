<?php

namespace App\Http\Controllers;

use App\Helpers\Privilege;
use App\Helpers\SafeUpload;
use App\Models\UnlistedNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UnlistedNewsController extends Controller
{
    public function index()
    {
        if (!$this->canAccess()) abort(403);

        return view('admin.unlisted.news');
    }

    public function data(Request $request)
    {
        if (!$this->canAccess()) abort(403);

        $query = UnlistedNews::query()->orderByDesc('published_at');

        if ($search = trim((string) $request->input('search', ''))) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        if ($type = $request->input('type', '')) {
            $query->where('type', $type);
        }

        if ($status = $request->input('status', '')) {
            $query->where('status', $status);
        }

        $news = $query->paginate(20);

        return view('admin.unlisted.partials.news-rows', compact('news'));
    }

    public function show(int $id)
    {
        if (!$this->canAccess()) abort(403);

        $news = UnlistedNews::findOrFail($id);
        $stocks = $news->unlistedStocks()->get(['unlisted_stocks.UL_STOCKS_FINCODE', 'unlisted_stocks.UL_STOCKS_COMPNAME']);

        return response()->json([
            'id'                  => $news->id,
            'title'               => $news->title,
            'type'                => $news->type,
            'link'                => $news->link,
            'video_preview_image' => $news->video_preview_image,
            'short_content'       => $news->short_content,
            'content'             => $news->content,
            'status'              => $news->status,
            'published_at'        => $news->published_at?->format('Y-m-d'),
            'published_hh'        => $news->published_at?->format('H'),
            'published_mm'        => $news->published_at?->format('i'),
            'stocks'              => $stocks,
        ]);
    }

    public function store(Request $request)
    {
        if (!$this->canAccess()) abort(403);

        $data = $this->validateNews($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['created_by'] = session('uid');

        $this->attachMedia($request, $data);

        $news = UnlistedNews::create($data);
        $news->unlistedStocks()->sync($request->input('stock_tickers', []));

        return response()->json(['success' => true, 'message' => 'News created successfully.', 'id' => $news->id]);
    }

    public function update(Request $request, int $id)
    {
        if (!$this->canAccess()) abort(403);

        $news = UnlistedNews::findOrFail($id);
        $data = $this->validateNews($request, $news->id);

        if ($request->hasFile('media')) {
            $this->deleteMediaFile($news->link);
        }

        $this->attachMedia($request, $data);

        $news->update($data);
        $news->unlistedStocks()->sync($request->input('stock_tickers', []));

        return response()->json(['success' => true, 'message' => 'News updated successfully.']);
    }

    public function changeStatus(Request $request, int $id)
    {
        if (!$this->canAccess()) abort(403);

        $request->validate(['status' => 'required|in:Active,Inactive']);

        $news = UnlistedNews::findOrFail($id);
        $news->update(['status' => $request->input('status')]);

        return response()->json(['success' => true, 'message' => 'Status updated.']);
    }

    public function uploadContentImage(Request $request)
    {
        if (!$this->canAccess()) abort(403);

        $request->validate(['file' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120']);

        $folder = SafeUpload::webRoot() . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'news-content';
        if (!is_dir($folder)) mkdir($folder, 0755, true);

        $file = $request->file('file');
        $ext  = SafeUpload::imageExtension($file);
        abort_if($ext === null, 422, 'Unsupported image type.');
        $filename = 'newscontent_' . time() . '_' . uniqid() . '.' . $ext;
        $file->move($folder, $filename);

        return response()->json(['location' => asset('images/news-content/' . $filename)]);
    }

    public function searchStocks(Request $request)
    {
        if (!Privilege::get('unlisted.news')) abort(403);

        $term = trim((string) $request->query('term', ''));

        $stocks = DB::table('unlisted_stocks')
            ->where('UL_STOCKS_STATUS', '1')
            ->when($term !== '', fn ($q) => $q->where('UL_STOCKS_COMPNAME', 'like', '%' . $term . '%'))
            ->orderBy('UL_STOCKS_COMPNAME')
            ->limit(20)
            ->get(['UL_STOCKS_FINCODE as fincode', 'UL_STOCKS_COMPNAME as name']);

        return response()->json($stocks);
    }

    private function validateNews(Request $request, ?int $ignoreId = null): array
    {
        $isCreate = $ignoreId === null;
        $type = $request->input('type', '');
        $mediaType = in_array($type, ['image', 'pdf', 'one_pager'], true);

        $data = $request->validate([
            'title'           => [
                'required', 'string', 'max:255',
                \Illuminate\Validation\Rule::unique('unlisted_news', 'title')->ignore($ignoreId),
            ],
            'type'            => 'required|in:text,image,video,pdf,one_pager',
            'media'           => [$isCreate && $mediaType ? 'required' : 'nullable', 'file'],
            // `url` alone doesn't restrict scheme (filter_var-based validation still
            // accepts e.g. `javascript:` URIs) — this is embedded straight into an
            // <iframe src="">, so pin it to https explicitly rather than trusting
            // "looks like a URL" to mean "safe to load".
            'video_link'      => ['required_if:type,video', 'nullable', 'url', 'starts_with:https://', 'max:500'],
            'short_content'   => 'required|string|max:1000',
            'content'         => 'required|string',
            'status'          => 'required|in:Active,Inactive',
            'published_date'  => 'required|date',
            'published_hh'    => 'required|integer|min:0|max:23',
            'published_mm'    => 'required|integer|min:0|max:59',
            'stock_tickers'   => 'nullable|array',
            'stock_tickers.*' => 'exists:unlisted_stocks,UL_STOCKS_FINCODE',
        ], [
            'title.unique' => 'A news item with this exact title already exists.',
            'media.required' => 'Please upload a file for this news type.',
        ]);

        // The editor produces rich HTML from an internal admin form, but it's still
        // attacker-controllable and rendered raw on the public news page — purified
        // via the same mews/purifier `clean()` helper already used for Articles
        // (CmsArticleController::validateArticle) and the Thesis rich-text field
        // (UnlistedStocksController::updateThesis), not a separate/new sanitizer.
        $data['content'] = clean($data['content']);

        $published = sprintf(
            '%s %02d:%02d:00',
            $data['published_date'],
            $data['published_hh'],
            $data['published_mm']
        );

        $out = [
            'title'         => $data['title'],
            'type'          => $data['type'],
            'short_content' => $data['short_content'],
            'content'       => $data['content'],
            'status'        => $data['status'],
            'published_at'  => $published,
        ];

        if ($data['type'] === 'video') {
            $out['link'] = $data['video_link'];
        }

        return $out;
    }

    // Handles the two upload fields independently — the main `media` file
    // (image/pdf/one_pager) and the video `video_preview` image are mutually
    // exclusive by type, but both must be checked here since only one of them
    // is ever present per request and neither implies the other was uploaded.
    private function attachMedia(Request $request, array &$data): void
    {
        $type = $data['type'];
        $folder = SafeUpload::webRoot() . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'news';
        if (!is_dir($folder)) mkdir($folder, 0755, true);

        if ($request->hasFile('media') && in_array($type, ['image', 'pdf', 'one_pager'], true)) {
            if ($type === 'pdf' || $type === 'one_pager') {
                $request->validate(['media' => 'mimes:pdf|max:10240']);
                $ext = SafeUpload::documentExtension($request->file('media'));
                abort_if($ext !== 'pdf', 422, 'Unsupported file type.');
            } else {
                $request->validate(['media' => 'image|mimes:jpg,jpeg,png,gif,webp|max:5120']);
                $ext = SafeUpload::imageExtension($request->file('media'));
                abort_if($ext === null, 422, 'Unsupported image type.');
            }

            $filename = 'news_' . time() . '_' . uniqid() . '.' . $ext;
            $request->file('media')->move($folder, $filename);
            $data['link'] = 'images/news/' . $filename;
        }

        if ($type === 'video' && $request->hasFile('video_preview')) {
            $request->validate(['video_preview' => 'image|mimes:jpg,jpeg,png,gif,webp|max:5120']);
            $previewExt = SafeUpload::imageExtension($request->file('video_preview'));
            abort_if($previewExt === null, 422, 'Unsupported image type.');
            $previewFilename = 'news_preview_' . time() . '_' . uniqid() . '.' . $previewExt;
            $request->file('video_preview')->move($folder, $previewFilename);
            $data['video_preview_image'] = 'images/news/' . $previewFilename;
        }
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (
            UnlistedNews::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    private function deleteMediaFile(?string $relativePath): void
    {
        if (!$relativePath || str_starts_with($relativePath, 'http')) return;

        foreach ([SafeUpload::webRoot(), public_path()] as $root) {
            $fullPath = $root . DIRECTORY_SEPARATOR . $relativePath;
            if (is_file($fullPath)) @unlink($fullPath);
        }
    }

    private function canAccess(): bool
    {
        return !empty(Privilege::get('admin'))
            || !empty(Privilege::get('unlisted.news'));
    }
}
