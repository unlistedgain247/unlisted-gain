@extends('layout.admin')

@section('title', 'Author Profile | CMS | Admin | UnlistedGain')

@php
    $initial = strtoupper(mb_substr($author->name, 0, 1));
@endphp

@section('content')
<div class="admin-main">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <h1 class="admin-page-title">Author Profile</h1>
        <a href="{{ route('admin.cms.articles') }}" class="cms-back-link"><i class="fa-solid fa-arrow-left"></i> Back to Articles</a>
    </div>

    @if(session('success'))
        <div class="cms-flash-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <div class="ap-grid">

        {{-- Form --}}
        <div class="admin-card">
            <p class="ap-hint">This bio and these social links appear on your author card at the bottom of every article you've published.</p>

            <form method="POST" action="{{ route('admin.cms.profile.update') }}">
                @csrf

                <div class="cms-field">
                    <label>Name</label>
                    <input type="text" value="{{ $author->name }}" class="cms-input cms-input-locked" readonly>
                </div>

                <div class="cms-field">
                    <label>About You</label>
                    <textarea name="author_bio" id="apBio" rows="4" class="cms-input" placeholder="A short bio shown on your articles...">{{ old('author_bio', $author->author_bio) }}</textarea>
                    @error('author_bio') <div class="cms-error">{{ $message }}</div> @enderror
                </div>

                <div class="ap-section-label">Social Links</div>
                <p class="ap-section-hint">Leave any of these blank to hide that icon from your author card.</p>

                <div class="ap-social-grid">
                    <div class="cms-field">
                        <label><span class="ap-icon ap-icon-li"><i class="fa-brands fa-linkedin-in"></i></span> LinkedIn</label>
                        <input type="url" name="author_linkedin" id="apLinkedin" value="{{ old('author_linkedin', $author->author_linkedin) }}" class="cms-input" placeholder="https://linkedin.com/in/yourname">
                        @error('author_linkedin') <div class="cms-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="cms-field">
                        <label><span class="ap-icon ap-icon-tw"><i class="fa-brands fa-x-twitter"></i></span> Twitter / X</label>
                        <input type="url" name="author_twitter" id="apTwitter" value="{{ old('author_twitter', $author->author_twitter) }}" class="cms-input" placeholder="https://x.com/yourname">
                        @error('author_twitter') <div class="cms-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="cms-field">
                        <label><span class="ap-icon ap-icon-fb"><i class="fa-brands fa-facebook-f"></i></span> Facebook</label>
                        <input type="url" name="author_facebook" id="apFacebook" value="{{ old('author_facebook', $author->author_facebook) }}" class="cms-input" placeholder="https://facebook.com/yourname">
                        @error('author_facebook') <div class="cms-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="cms-field">
                        <label><span class="ap-icon ap-icon-ig"><i class="fa-brands fa-instagram"></i></span> Instagram</label>
                        <input type="url" name="author_instagram" id="apInstagram" value="{{ old('author_instagram', $author->author_instagram) }}" class="cms-input" placeholder="https://instagram.com/yourname">
                        @error('author_instagram') <div class="cms-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="cms-field ap-social-full">
                        <label><span class="ap-icon ap-icon-web"><i class="fa-solid fa-globe"></i></span> Website</label>
                        <input type="url" name="author_website" id="apWebsite" value="{{ old('author_website', $author->author_website) }}" class="cms-input" placeholder="https://yourwebsite.com">
                        @error('author_website') <div class="cms-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button type="submit" class="cms-submit-btn ap-save-btn"><i class="fa-solid fa-check"></i> Save Profile</button>
            </form>
        </div>

        {{-- Live preview --}}
        <div class="ap-preview-col">
            <div class="admin-card ap-preview-card">
                <div class="ap-preview-label"><i class="fa-solid fa-eye"></i> Live Preview</div>

                <div class="ap-preview-box">
                    <div class="ap-preview-avatar">{{ $initial }}</div>
                    <div class="ap-preview-body">
                        <h4>{{ $author->name }}</h4>
                        <p id="apPreviewBio">{{ $author->author_bio ?: 'Contributor at UnlistedGain, covering unlisted shares, pre-IPO investing and market insights.' }}</p>
                        <div class="ap-preview-socials" id="apPreviewSocials">
                            @if($author->author_linkedin)
                                <span class="ap-icon ap-icon-li" data-social="linkedin"><i class="fa-brands fa-linkedin-in"></i></span>
                            @endif
                            @if($author->author_twitter)
                                <span class="ap-icon ap-icon-tw" data-social="twitter"><i class="fa-brands fa-x-twitter"></i></span>
                            @endif
                            @if($author->author_facebook)
                                <span class="ap-icon ap-icon-fb" data-social="facebook"><i class="fa-brands fa-facebook-f"></i></span>
                            @endif
                            @if($author->author_instagram)
                                <span class="ap-icon ap-icon-ig" data-social="instagram"><i class="fa-brands fa-instagram"></i></span>
                            @endif
                            @if($author->author_website)
                                <span class="ap-icon ap-icon-web" data-social="website"><i class="fa-solid fa-globe"></i></span>
                            @endif
                        </div>
                    </div>
                </div>

                <p class="ap-preview-note">This is exactly how your card appears at the bottom of your published articles.</p>
            </div>
        </div>

    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/cms-author-profile.css') }}?v={{ filemtime(public_path('assets/css/admin/cms-author-profile.css')) }}">
@endpush

<script>
(function () {
    var bioInput = document.getElementById('apBio');
    var previewBio = document.getElementById('apPreviewBio');
    var defaultBio = 'Contributor at UnlistedGain, covering unlisted shares, pre-IPO investing and market insights.';

    if (bioInput && previewBio) {
        bioInput.addEventListener('input', function () {
            previewBio.textContent = bioInput.value.trim() || defaultBio;
        });
    }

    var socialInputs = {
        linkedin: document.getElementById('apLinkedin'),
        twitter: document.getElementById('apTwitter'),
        facebook: document.getElementById('apFacebook'),
        instagram: document.getElementById('apInstagram'),
        website: document.getElementById('apWebsite'),
    };
    var previewSocials = document.getElementById('apPreviewSocials');

    var iconHtml = {
        linkedin: '<span class="ap-icon ap-icon-li" data-social="linkedin"><i class="fa-brands fa-linkedin-in"></i></span>',
        twitter: '<span class="ap-icon ap-icon-tw" data-social="twitter"><i class="fa-brands fa-x-twitter"></i></span>',
        facebook: '<span class="ap-icon ap-icon-fb" data-social="facebook"><i class="fa-brands fa-facebook-f"></i></span>',
        instagram: '<span class="ap-icon ap-icon-ig" data-social="instagram"><i class="fa-brands fa-instagram"></i></span>',
        website: '<span class="ap-icon ap-icon-web" data-social="website"><i class="fa-solid fa-globe"></i></span>',
    };

    function renderSocials() {
        if (!previewSocials) return;
        var html = '';
        Object.keys(socialInputs).forEach(function (key) {
            var el = socialInputs[key];
            if (el && el.value.trim() !== '') html += iconHtml[key];
        });
        previewSocials.innerHTML = html;
    }

    Object.values(socialInputs).forEach(function (el) {
        if (el) el.addEventListener('input', renderSocials);
    });
})();
</script>
@endsection
