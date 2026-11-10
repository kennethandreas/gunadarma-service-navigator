<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Show the About page.
     */
    public function about(): View
    {
        return view('pages.about');
    }
}
