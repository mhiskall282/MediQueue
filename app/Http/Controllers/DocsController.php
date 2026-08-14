<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DocsController extends Controller
{
    /**
     * Interactive Software Engineering Documentation & Project Hub.
     */
    public function index(Request $request): View
    {
        $section = $request->query('section', 'overview');

        return view('docs.index', compact('section'));
    }
}
