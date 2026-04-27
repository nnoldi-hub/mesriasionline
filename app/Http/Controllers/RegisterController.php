<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use App\Models\Location;
use App\Services\AffiliateService;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        
        return view('auth.register', compact('categories', 'locations'));
    }

    public function register(Request $request, ImageCompressionService $imageService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'experience_years' => 'required|integer|min:0|max:50',
            'g-recaptcha-response' => 'required|captcha',
            'specialization' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'service_radius_km' => 'nullable|integer|min:5|max:100',
            'available_weekends' => 'boolean',
            'emergency_services' => 'boolean',
            'has_insurance' => 'boolean',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Generate unique slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        
        while (User::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle profile photo if uploaded
        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $imageService->processProfilePhoto($request->file('profile_photo'));
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $validated['phone'],
            'role' => 'specialist',
            'category_id' => $validated['category_id'],
            'location_id' => $validated['location_id'],
            'slug' => $slug,
            'experience_years' => $validated['experience_years'],
            'specialization' => $validated['specialization'],
            'description' => $validated['description'],
            'service_radius_km' => $validated['service_radius_km'] ?? 30,
            'available_weekends' => $request->boolean('available_weekends'),
            'emergency_services' => $request->boolean('emergency_services'),
            'has_insurance' => $request->boolean('has_insurance'),
            'profile_photo' => $profilePhotoPath,
            'is_active' => false, // Needs admin approval
            'verified_at' => null,
        ]);

        // Attribute registration to affiliate if referral cookie exists
        app(AffiliateService::class)->attributeRegistration($user);

        // Auto-login after registration
        auth()->login($user);

        return redirect()->route('craftsman.dashboard')
            ->with('success', 'Contul tău a fost creat cu succes! Un administrator va verifica și activa contul tău în curând.');
    }
}
