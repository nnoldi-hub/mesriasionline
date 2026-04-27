<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientMiddleware
{
    /**
     * Handle an incoming request.
     * Verifică dacă utilizatorul autentificat este un client.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Verifică dacă utilizatorul are rolul de client
        if (auth()->user()->role !== 'client') {
            // Redirecționează către dashboard-ul corespunzător rolului
            if (auth()->user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            if (auth()->user()->role === 'specialist') {
                return redirect()->route('craftsman.dashboard');
            }
            
            // Pentru orice alt rol necunoscut
            abort(403, 'Acces interzis. Această secțiune este destinată doar clienților.');
        }

        return $next($request);
    }
}
