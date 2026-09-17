<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\UnlistedStock;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    private const STATIC_ROUTES = [
        'public.home'                 => '1.0',
        'public.buy'                  => '0.9',
        'public.pre-ipo'              => '0.8',
        'public.price-list'           => '0.8',
        'public.sell'                 => '0.8',
        'public.articles'             => '0.7',
        'public.news'                 => '0.7',
        'public.knowledge-centre'     => '0.6',
        'public.about'                => '0.6',
        'public.connect'              => '0.6',
        'public.faq'                  => '0.5',
        'public.sebi-guidelines'      => '0.4',
        'public.off-market-annexure'  => '0.4',
        'public.pan-unlisted-shares'  => '0.4',
        'public.bank-account-details' => '0.4',
        'public.privacy-policy'       => '0.3',
        'public.terms-of-use'         => '0.3',
    ];

    public function index()
    {
        $urls = Cache::remember('sitemap.xml.urls', 3600, function () {
            $urls = collect();

            foreach (self::STATIC_ROUTES as $name => $priority) {
                $urls->push([
                    'loc'      => route($name),
                    'lastmod'  => now(),
                    'priority' => $priority,
                ]);
            }

            UnlistedStock::query()
                ->where('UL_STOCKS_STATUS', '1')
                ->whereNotNull('UL_STOCKS_SLUG')
                ->select('UL_STOCKS_SLUG', 'UL_STOCKS_UPDATE_TIME')
                ->orderBy('UL_STOCKS_SLUG')
                ->each(function ($stock) use ($urls) {
                    $urls->push([
                        'loc'      => route('public.company', $stock->UL_STOCKS_SLUG),
                        'lastmod'  => $stock->UL_STOCKS_UPDATE_TIME ?? now(),
                        'priority' => '0.9',
                    ]);
                });

            Article::published()
                ->select('slug', 'updated_at')
                ->orderBy('slug')
                ->each(function ($article) use ($urls) {
                    $urls->push([
                        'loc'      => route('public.articles.show', $article->slug),
                        'lastmod'  => $article->updated_at ?? now(),
                        'priority' => '0.7',
                    ]);
                });

            $authorUids = Article::published()->distinct()->pluck('created_by');

            User::whereIn('uid', $authorUids)
                ->select('uid', 'name')
                ->get()
                ->each(function ($author) use ($urls) {
                    $urls->push([
                        'loc'      => route('public.authors.show', [$author->uid, Str::slug($author->name)]),
                        'lastmod'  => now(),
                        'priority' => '0.5',
                    ]);
                });

            return $urls;
        });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $loc      = htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8');
            $lastmod  = Carbon::parse($url['lastmod'])->format('Y-m-d');
            $priority = $url['priority'];

            $xml .= "    <url>\n";
            $xml .= "        <loc>{$loc}</loc>\n";
            $xml .= "        <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "        <priority>{$priority}</priority>\n";
            $xml .= "    </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'text/xml; charset=UTF-8');
    }
}
