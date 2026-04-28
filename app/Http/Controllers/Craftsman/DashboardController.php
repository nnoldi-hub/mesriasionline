<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Review;
use App\Models\Gallery;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $craftsman = auth()->user();

        $stats = [
            'total_services' => $craftsman->services()->count(),
            'active_services' => $craftsman->services()->where('is_active', true)->count(),
            'total_appointments' => $craftsman->appointments()->count(),
            'pending_appointments' => $craftsman->appointments()->where('status', 'pending')->count(),
            'completed_appointments' => $craftsman->appointments()->where('status', 'completed')->count(),
            'total_reviews' => $craftsman->reviews()->count(),
            'average_rating' => $craftsman->reviews()->avg('rating') ?? 0,
        ];

        $recent_appointments = $craftsman->appointments()
            ->with('service')
            ->latest()
            ->take(5)
            ->get();

        $recent_reviews = $craftsman->reviews()
            ->where('is_approved', true)
            ->with('appointment')
            ->latest()
            ->take(5)
            ->get();

        return view('craftsman.dashboard', compact('stats', 'recent_appointments', 'recent_reviews', 'craftsman'));
    }

    public function services()
    {
        $craftsman = auth()->user();
        $services = $craftsman->services()->withCount('appointments')->paginate(20);

        return view('craftsman.services.index', compact('services'));
    }

    public function appointments(Request $request)
    {
        $craftsman = auth()->user();
        
        $query = $craftsman->appointments()->with('service');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $appointments = $query->latest()->paginate(20);

        return view('craftsman.appointments.index', compact('appointments'));
    }

    public function reviews()
    {
        $craftsman = auth()->user();
        $reviews = $craftsman->reviews()
            ->where('is_approved', true)
            ->with('appointment')
            ->latest()
            ->paginate(20);

        return view('craftsman.reviews.index', compact('reviews'));
    }

    public function profile()
    {
        $craftsman = auth()->user();
        return view('craftsman.profile', compact('craftsman'));
    }

    public function updateProfile(Request $request, ImageCompressionService $imageService)
    {
        $craftsman = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'description' => 'nullable|string',
            'experience_years' => 'nullable|integer|min:0',
            'specialization' => 'nullable|string|max:255',
            'service_radius_km' => 'nullable|integer|min:0',
            'available_weekends' => 'boolean',
            'emergency_services' => 'boolean',
            'has_insurance' => 'boolean',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($craftsman->profile_photo) {
                Storage::disk('public')->delete($craftsman->profile_photo);
            }

            // Compress and save new photo
            $validated['profile_photo'] = $imageService->processProfilePhoto($request->file('profile_photo'));
        }

        $craftsman->update($validated);

        return back()->with('success', 'Profilul a fost actualizat cu succes.');
    }

    // Services CRUD
    public function createService()
    {
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        return view('craftsman.services.create', compact('categories'));
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'detailed_description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'pricing_type' => 'required|in:fixed,range,on_request',
            'price' => 'nullable|numeric|min:0',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:0',
            'min_duration' => 'nullable|integer|min:0',
            'max_duration' => 'nullable|integer|min:0',
        ]);

        $craftsman = auth()->user();

        $service = $craftsman->services()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'detailed_description' => $validated['detailed_description'] ?? null,
            'category_id' => $validated['category_id'] ?? $craftsman->category_id,
            'pricing_type' => $validated['pricing_type'],
            'price' => $validated['pricing_type'] === 'fixed' ? $validated['price'] : null,
            'min_price' => $validated['pricing_type'] === 'range' ? $validated['min_price'] : null,
            'max_price' => $validated['pricing_type'] === 'range' ? $validated['max_price'] : null,
            'duration' => $validated['duration'] ?? null,
            'min_duration' => $validated['min_duration'] ?? null,
            'max_duration' => $validated['max_duration'] ?? null,
            'is_active' => $request->has('is_active'),
            'is_on_location' => $request->has('is_mobile'),
            'materials_included' => $request->has('materials_included'),
        ]);

        return redirect()->route('craftsman.services')->with('success', 'Serviciul a fost creat cu succes!');
    }

    public function editService($id)
    {
        $craftsman = auth()->user();
        $service = $craftsman->services()->findOrFail($id);
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();

        return view('craftsman.services.edit', compact('service', 'categories'));
    }

    public function updateService(Request $request, $id)
    {
        $craftsman = auth()->user();
        $service = $craftsman->services()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'detailed_description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'pricing_type' => 'required|in:fixed,range,on_request',
            'price' => 'nullable|numeric|min:0',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:0',
            'min_duration' => 'nullable|integer|min:0',
            'max_duration' => 'nullable|integer|min:0',
        ]);

        $service->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? $service->description,
            'detailed_description' => $validated['detailed_description'] ?? $service->detailed_description,
            'category_id' => $validated['category_id'] ?? $service->category_id,
            'pricing_type' => $validated['pricing_type'],
            'price' => $validated['pricing_type'] === 'fixed' ? $validated['price'] : null,
            'min_price' => $validated['pricing_type'] === 'range' ? $validated['min_price'] : null,
            'max_price' => $validated['pricing_type'] === 'range' ? $validated['max_price'] : null,
            'duration' => $validated['duration'] ?? $service->duration,
            'min_duration' => $validated['min_duration'] ?? $service->min_duration,
            'max_duration' => $validated['max_duration'] ?? $service->max_duration,
            'is_active' => $request->has('is_active'),
            'is_on_location' => $request->has('is_mobile'),
            'materials_included' => $request->has('materials_included'),
        ]);

        return redirect()->route('craftsman.services')->with('success', 'Serviciul a fost actualizat cu succes!');
    }

    public function deleteService($id)
    {
        $craftsman = auth()->user();
        $service = $craftsman->services()->findOrFail($id);

        // Check if service has appointments
        if ($service->appointments()->count() > 0) {
            return back()->with('error', 'Nu poți șterge un serviciu care are programări.');
        }

        $service->delete();

        return redirect()->route('craftsman.services')->with('success', 'Serviciul a fost șters.');
    }

    public function toggleServiceStatus($id)
    {
        $craftsman = auth()->user();
        $service = $craftsman->services()->findOrFail($id);
        $service->update(['is_active' => !$service->is_active]);

        $status = $service->is_active ? 'activat' : 'dezactivat';
        return back()->with('success', "Serviciul a fost {$status}.");
    }

    // Gallery Management
    public function gallery(Request $request)
    {
        $craftsman = auth()->user();
        
        $query = $craftsman->gallery()->orderBy('sort_order')->orderBy('created_at', 'desc');
        
        // Filter by category if specified
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        
        $images = $query->paginate(20);
        
        // Get categories that have images for the filter
        $categories = $craftsman->gallery()->whereNotNull('category')->pluck('category')->unique();
        
        // Get counts per category
        $categoryCounts = $craftsman->gallery()
            ->whereNotNull('category')
            ->selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category');
        
        return view('craftsman.gallery.index', compact('images', 'categories', 'categoryCounts'));
    }

    public function uploadGallery()
    {
        $craftsman = auth()->user();
        $services = $craftsman->services()->where('is_active', true)->get();
        
        return view('craftsman.gallery.upload', compact('services'));
    }

    public function storeGallery(Request $request, ImageCompressionService $imageService)
    {
        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'caption' => 'nullable|string|max:255',
            'service_id' => 'nullable|exists:services,id',
            'category' => 'nullable|string|in:' . implode(',', array_keys(Gallery::CATEGORIES)),
            'before_after' => 'required|in:single,before,after',
            'is_featured' => 'boolean',
        ]);

        $craftsman = auth()->user();
        $uploadedCount = 0;

        foreach ($request->file('images') as $image) {
            // Compress and optimize image
            $result = $imageService->processGalleryImage($image);

            Gallery::create([
                'user_id' => $craftsman->id,
                'service_id' => $request->service_id,
                'category' => $request->category,
                'image_path' => $result['path'],
                'caption' => $request->caption,
                'sub_brand' => $craftsman->sub_brand,
                'before_after' => $request->before_after,
                'is_featured' => $request->has('is_featured'),
                'sort_order' => $craftsman->gallery()->count(),
            ]);

            $uploadedCount++;
        }

        return redirect()->route('craftsman.gallery')->with('success', "{$uploadedCount} imagini au fost încărcate cu succes!");
    }

    public function editGalleryImage($id)
    {
        $craftsman = auth()->user();
        $image = $craftsman->gallery()->findOrFail($id);
        $services = $craftsman->services()->where('is_active', true)->get();

        return view('craftsman.gallery.edit', compact('image', 'services'));
    }

    public function updateGalleryImage(Request $request, $id)
    {
        $request->validate([
            'caption' => 'nullable|string|max:255',
            'service_id' => 'nullable|exists:services,id',
            'category' => 'nullable|string|in:' . implode(',', array_keys(Gallery::CATEGORIES)),
            'before_after' => 'required|in:single,before,after',
            'is_featured' => 'boolean',
        ]);

        $craftsman = auth()->user();
        $image = $craftsman->gallery()->findOrFail($id);

        $image->update([
            'caption' => $request->caption,
            'service_id' => $request->service_id,
            'category' => $request->category,
            'before_after' => $request->before_after,
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('craftsman.gallery')->with('success', 'Imaginea a fost actualizată!');
    }

    public function deleteGalleryImage($id)
    {
        $craftsman = auth()->user();
        $image = $craftsman->gallery()->findOrFail($id);

        // Delete file from storage
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return back()->with('success', 'Imaginea a fost ștearsă.');
    }

    public function toggleFeaturedImage($id)
    {
        $craftsman = auth()->user();
        $image = $craftsman->gallery()->findOrFail($id);
        $image->update(['is_featured' => !$image->is_featured]);

        $status = $image->is_featured ? 'adăugată la' : 'eliminată din';
        return back()->with('success', "Imaginea a fost {$status} favorite.");
    }

    // Social Media
    public function socialMedia()
    {
        $craftsman = auth()->user();
        return view('craftsman.social-media', compact('craftsman'));
    }

    public function updateSocialMedia(Request $request)
    {
        $validated = $request->validate([
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'website_url' => 'nullable|url|max:255',
        ]);

        $craftsman = auth()->user();
        $craftsman->update($validated);

        return back()->with('success', 'Link-urile social media au fost actualizate!');
    }
}
