<?php

namespace App\Http\Controllers;

use App\Models\UnlistedNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    public function index()
    {
        $totalCount = UnlistedNews::active()->count();
        $latestFive = UnlistedNews::active()->orderByDesc('published_at')->take(5)->get();

        return view('public.news.index', compact('totalCount', 'latestFive'));
    }

    public function data(Request $request)
    {
        $query = UnlistedNews::active()->with('unlistedStocks');

        if ($fincode = $request->input('fincode')) {
            // Unqualified would be ambiguous: MySQL resolves column names
            // case-insensitively, so `UL_STOCKS_FINCODE` (unlisted_stocks) and
            // `ul_stocks_fincode` (the pivot table, also joined into this
            // whereHas subquery) count as the same identifier.
            $query->whereHas('unlistedStocks', fn ($q) => $q->where('unlisted_stocks.UL_STOCKS_FINCODE', $fincode));
        }

        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('short_content', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        if ($from = $request->input('start_date')) {
            $query->whereDate('published_at', '>=', $from);
        }

        if ($to = $request->input('end_date')) {
            $query->whereDate('published_at', '<=', $to);
        }

        if ($request->boolean('video_only')) {
            $query->where('type', 'video');
        }

        $news = $query->orderByDesc('published_at')->paginate(12);

        return view('public.news.partials.news-grid', compact('news'));
    }

    public function show(int $id)
    {
        $news = UnlistedNews::active()->with('unlistedStocks')->findOrFail($id);

        return view('public.news.partials.news-detail', compact('news'));
    }

    public function searchStocks(Request $request)
    {
        $term = trim((string) $request->query('q', ''));
        if (strlen($term) < 1) return response()->json([]);

        $stocks = DB::table('unlisted_stocks')
            ->where('UL_STOCKS_STATUS', '1')
            ->where('UL_STOCKS_COMPNAME', 'like', '%' . $term . '%')
            ->orderBy('UL_STOCKS_COMPNAME')
            ->limit(20)
            ->get(['UL_STOCKS_FINCODE as fincode', 'UL_STOCKS_COMPNAME as name']);

        return response()->json($stocks);
    }
}
