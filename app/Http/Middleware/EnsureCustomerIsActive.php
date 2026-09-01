<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerIsActive
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        if (! $request->user()?->isActiveCustomer()) {
            return redirect()->route('approval.pending');
        }

        return $next($request);
    }
}
