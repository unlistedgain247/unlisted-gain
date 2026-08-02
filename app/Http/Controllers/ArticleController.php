<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::published()
            ->with('author', 'unlistedStocks')
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('public.articles.index', compact('articles'));
    }

    public function show(string $slug)
    {
        $article = Article::published()
            ->where('slug', $slug)
            ->with('author', 'unlistedStocks')
            ->firstOrFail();

        return view('public.articles.show', compact('article'));
    }
}
