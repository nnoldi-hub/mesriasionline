<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpecialistMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'specialist') {
            return redirect()->route('login')->with('error', 'Acces interzis. Doar meseriașii pot accesa această pagină.');
        }

        return $next($request);
    }
}
