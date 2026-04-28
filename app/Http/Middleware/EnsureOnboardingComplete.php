<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingComplete
{
    /**
     * Redirecționează meseriașii care nu și-au finalizat onboarding-ul.
     * Se aplică pe rutele de dashboard craftsman.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'specialist') {
            return $next($request);
        }

        // Dacă onboarding-ul nu e completat, trimite la pasul curent
        if (!$user->onboarding_completed_at) {
            // Conturile activate de admin (is_active=true) se consideră finalizate
            if ($user->is_active) {
                $user->update(['onboarding_completed_at' => now(), 'onboarding_step' => 4]);
                return $next($request);
            }

            $step = max(1, (int) $user->onboarding_step);

            // Evită redirect loop dacă deja e pe o rută de onboarding
            if (!$request->routeIs('onboarding.*')) {
                return redirect()->route('onboarding.step', ['step' => $step])
                    ->with('info', 'Completează-ți profilul pentru a accesa dashboard-ul.');
            }
        }

        return $next($request);
    }
}
