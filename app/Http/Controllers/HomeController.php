<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()) {
            return redirect()->route($request->user()->isAdmin() ? 'admin.dashboard' : 'dashboard');
        }

        return view('home');
    }
}
