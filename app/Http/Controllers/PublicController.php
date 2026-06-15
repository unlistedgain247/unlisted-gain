<?php

namespace App\Http\Controllers;

class PublicController extends Controller
{
    public function welcome()
    {
        return view('public.welcome');
    }

    public function about()
    {
        return view('public.about');
    }

    public function connect()
    {
        return view('public.connect');
    }

    public function privacyPolicy()
    {
        return view('public.privacy-policy');
    }

    public function termsOfUse()
    {
        return view('public.terms-of-use');
    }

    public function offMarketAnnexure()
    {
        return view('public.off-market-annexure');
    }

    public function panUnlistedShares()
    {
        return view('public.pan-unlisted-shares');
    }

    public function sebiGuidelines()
    {
        return view('public.sebi-guidelines');
    }

    public function knowledgeCentre()
    {
        return view('public.knowledge-centre');
    }

    public function faq()
    {
        return view('public.faq');
    }
}
