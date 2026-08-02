<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;

class AuthorController extends Controller
{
    public function show(int $uid)
    {
        $author = User::findOrFail($uid);

        // Only expose this page for users who have actually published something —
        // keeps random customer accounts from being reachable via a guessed uid.
        abort_unless(
            Article::published()->where('created_by', $author->uid)->exists(),
            404
        );

        $articles = Article::published()
            ->where('created_by', $author->uid)
            ->with('unlistedStocks')
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('public.authors.show', compact('author', 'articles'));
    }
}
