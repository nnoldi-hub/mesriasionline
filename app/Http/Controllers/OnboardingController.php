<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Location;
use App\Models\Service;
use App\Models\User;
use App\Services\AffiliateService;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    // ──────────────────────────────────────────────
    // ÎNREGISTRARE RAPIDĂ (doar email + parolă)
    // ──────────────────────────────────────────────

    public function showQuickRegister()
    {
        if (auth()->check()) {
            return $this->redirectToCurrentStep(auth()->user());
        }

        return view('onboarding.quick-register');
    }

    public function quickRegister(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|max:255|unique:users',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        // Generate unique slug
        $slug = Str::slug($validated['name']);
        $base = $slug;
        $i = 1;
        while (User::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $user = User::create([
            'name'            => $validated['name'],
            'email'           => $validated['email'],
            'password'        => $validated['password'],
            'role'            => 'specialist',
            'is_active'       => false,
            'slug'            => $slug,
            'onboarding_step' => 1,
        ]);

        app(AffiliateService::class)->attributeRegistration($user);

        Auth::login($user);

        return redirect()->route('onboarding.step', ['step' => 1]);
    }

    // ──────────────────────────────────────────────
    // DISPATCHER — trimite la metoda corectă
    // ──────────────────────────────────────────────

    public function saveStep(Request $request, int $step, ImageCompressionService $imageService)
    {
        return match ($step) {
            1 => $this->saveStep1($request),
            2 => $this->saveStep2($request),
            3 => $this->saveStep3($request, $imageService),
            4 => $this->saveStep4($request),
            default => abort(404),
        };
    }

    // ──────────────────────────────────────────────
    // AFIȘARE PAS
    // ──────────────────────────────────────────────

    public function showStep(int $step)
    {
        $user = auth()->user();

        if ($user->onboarding_completed_at) {
            return redirect()->route('craftsman.dashboard');
        }

        // Nu permite salt înainte
        if ($step > $user->onboarding_step) {
            return redirect()->route('onboarding.step', ['step' => max(1, $user->onboarding_step)]);
        }

        // Nu permite să meargă înapoi sub pasul curent salvat (dar permite navigare între pași deja făcuți)
        $step = max(1, min(4, $step));

        $data = match ($step) {
            1 => ['categories' => Category::orderBy('name')->get(), 'locations' => Location::orderBy('city')->get()],
            2 => [],
            3 => [],
            4 => [],
        };

        return view("onboarding.step{$step}", array_merge(['currentStep' => $step, 'user' => $user], $data));
    }

    // ──────────────────────────────────────────────
    // PAS 1 — Date personale (telefon, categorie, locație)
    // ──────────────────────────────────────────────

    public function saveStep1(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'phone'       => 'required|string|max:20',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
        ]);

        $user->update(array_merge($validated, [
            'onboarding_step' => max($user->onboarding_step, 2),
        ]));

        return redirect()->route('onboarding.step', ['step' => 2]);
    }

    // ──────────────────────────────────────────────
    // PAS 2 — Primul serviciu (titlu + preț)
    // ──────────────────────────────────────────────

    public function saveStep2(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'service_name'  => 'required|string|max:255',
            'service_price' => 'required|numeric|min:0',
            'service_desc'  => 'nullable|string|max:500',
        ]);

        Service::updateOrCreate(
            ['user_id' => $user->id, 'name' => $validated['service_name']],
            [
                'name'        => $validated['service_name'],
                'description' => $validated['service_desc'] ?? $validated['service_name'],
                'price'       => $validated['service_price'],
                'is_active'   => true,
            ]
        );

        $user->update(['onboarding_step' => max($user->onboarding_step, 3)]);

        return redirect()->route('onboarding.step', ['step' => 3]);
    }

    // ──────────────────────────────────────────────
    // PAS 3 — Poză profil
    // ──────────────────────────────────────────────

    public function saveStep3(Request $request, ImageCompressionService $imageService)
    {
        $user = auth()->user();

        $request->validate([
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $imageService->processProfilePhoto($request->file('profile_photo'));
            $user->update(['profile_photo' => $path]);
        }

        $user->update(['onboarding_step' => max($user->onboarding_step, 4)]);

        return redirect()->route('onboarding.step', ['step' => 4]);
    }

    // ──────────────────────────────────────────────
    // PAS 4 — Disponibilitate implicită
    // ──────────────────────────────────────────────

    public function saveStep4(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'work_days'        => 'required|array|min:1',
            'work_days.*'      => 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
            'work_start'       => 'required|date_format:H:i',
            'work_end'         => 'required|date_format:H:i|after:work_start',
            'available_weekends' => 'boolean',
        ]);

        $schedule = [];
        foreach ($request->work_days as $day) {
            $schedule[$day] = [
                'start' => $request->work_start,
                'end'   => $request->work_end,
            ];
        }

        $user->update([
            'weekly_schedule'       => $schedule,
            'available_weekends'    => $request->boolean('available_weekends'),
            'onboarding_step'       => 4,
            'onboarding_completed_at' => now(),
        ]);

        return redirect()->route('craftsman.dashboard')
            ->with('success', 'Profilul tău a fost creat cu succes! Un administrator va activa contul tău în curând.');
    }

    // ──────────────────────────────────────────────
    // Helper: redirect la pasul curent
    // ──────────────────────────────────────────────

    private function redirectToCurrentStep(User $user): \Illuminate\Http\RedirectResponse
    {
        if ($user->onboarding_completed_at) {
            return redirect()->route('craftsman.dashboard');
        }

        return redirect()->route('onboarding.step', ['step' => max(1, $user->onboarding_step)]);
    }
}
