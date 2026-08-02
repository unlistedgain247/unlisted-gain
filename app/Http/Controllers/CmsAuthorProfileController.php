<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CmsAuthorProfileController extends Controller
{
    public function edit()
    {
        $author = User::findOrFail(session('uid'));

        return view('admin.cms.author-profile', compact('author'));
    }

    public function update(Request $request)
    {
        $author = User::findOrFail(session('uid'));

        $data = $request->validate([
            'author_bio'       => 'nullable|string|max:1000',
            'author_linkedin'  => 'nullable|url|max:255',
            'author_twitter'   => 'nullable|url|max:255',
            'author_facebook'  => 'nullable|url|max:255',
            'author_instagram' => 'nullable|url|max:255',
            'author_website'   => 'nullable|url|max:255',
        ]);

        $author->update($data);

        return redirect()->route('admin.cms.profile.edit')->with('success', 'Author profile updated.');
    }
}
