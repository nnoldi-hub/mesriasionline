<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Review;
use App\Models\Category;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function editProfile()
    {
        $admin = auth()->user();
        return view('admin.profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = auth()->user();
        $request->validate([
            'email' => 'required|email',
            'password' => 'nullable|min:6|confirmed',
        ]);
        $admin->email = $request->email;
        if ($request->filled('password')) {
            $admin->password = bcrypt($request->password);
        }
        $admin->save();
        return redirect()->route('admin.profile')->with('success', 'Datele au fost actualizate!');
    }
    // Marchează o solicitare generică ca rezolvată
    public function completeGenericRequest($id)
    {
        $request = \App\Models\Appointment::whereNull('service_id')->findOrFail($id);
        $request->status = 'completed';
        $request->save();
        return back()->with('success', 'Solicitarea a fost marcată ca rezolvată.');
    }
        // Listare solicitări generice de mentenanță/întreținere
        public function genericRequests()
        {
            $requests = \App\Models\Appointment::whereNull('service_id')
                ->orderByDesc('created_at')
                ->get();
            return view('admin.generic_requests', compact('requests'));
        }
    public function index()
    {
        $stats = [
            'total_craftsmen' => User::where('role', 'specialist')->count(),
            'active_craftsmen' => User::where('role', 'specialist')->where('is_active', true)->count(),
            'total_services' => Service::count(),
            'maintenance_services' => Service::whereHas('category', function($q){ $q->where('name', 'Intretinere imobile'); })->count(),
            'technical_services' => Service::whereHas('category', function($q){ $q->where('name', 'Mentenanta'); })->count(),
            'total_appointments' => Appointment::count(),
            'pending_appointments' => Appointment::where('status', 'pending')->count(),
            'total_reviews' => Review::count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
            'total_categories' => Category::count(),
            // Număr solicitări generice neprocesate
            'pending_generic_requests' => Appointment::whereNull('service_id')->where('status', 'pending')->count(),
        ];

        $recent_craftsmen = User::where('role', 'specialist')
            ->with('category', 'location')
            ->latest()
            ->take(5)
            ->get();

        $pending_reviews = Review::where('is_approved', false)
            ->with('specialist', 'appointment')
            ->latest()
            ->take(5)
            ->get();

        $recent_appointments = Appointment::with('specialist', 'service')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_craftsmen', 'pending_reviews', 'recent_appointments'));
    }

    public function craftsmen(Request $request)
    {
        $query = User::where('role', 'specialist')
            ->with(['category', 'location', 'services', 'reviews']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        if ($request->has('is_featured') && $request->is_featured !== '') {
            $query->where('is_featured', $request->is_featured);
        }

        $craftsmen = $query->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('is_featured') // Featured first
            ->latest()
            ->paginate(20);

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.craftsmen.index', compact('craftsmen', 'categories'));
    }

    public function reviews(Request $request)
    {
        $query = Review::with('specialist', 'appointment');

        if ($request->has('is_approved')) {
            $query->where('is_approved', $request->is_approved);
        }

        $reviews = $query->latest()->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approveReview($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Recenzia a fost aprobată.');
    }

    public function toggleCraftsmanStatus($id)
    {
        $craftsman = User::where('role', 'specialist')->findOrFail($id);
        $craftsman->update(['is_active' => !$craftsman->is_active]);

        $status = $craftsman->is_active ? 'activat' : 'dezactivat';
        return back()->with('success', "Meseriașul a fost {$status}.");
    }

    // Services management
    public function services(Request $request)
    {
        $query = Service::with(['user', 'category']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $services = $query->latest()->paginate(20);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.services.index', compact('services', 'categories'));
    }

    public function editService($id)
    {
        $service = Service::with(['user', 'category'])->findOrFail($id);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.services.edit', compact('service', 'categories'));
    }

    public function updateService(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'detailed_description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'pricing_type' => 'nullable|in:fixed,range,on_request',
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
            'pricing_type' => $validated['pricing_type'] ?? $service->pricing_type,
            'price' => $validated['price'] ?? $service->price,
            'min_price' => $validated['min_price'] ?? $service->min_price,
            'max_price' => $validated['max_price'] ?? $service->max_price,
            'duration' => $validated['duration'] ?? $service->duration,
            'min_duration' => $validated['min_duration'] ?? $service->min_duration,
            'max_duration' => $validated['max_duration'] ?? $service->max_duration,
            'is_active' => $request->has('is_active'),
            'is_on_location' => $request->has('is_mobile'),
            'materials_included' => $request->has('materials_included'),
        ]);

        return redirect()->route('admin.services.edit', $service->id)->with('success', 'Serviciul a fost actualizat cu succes.');
    }

    public function toggleServiceStatus($id)
    {
        $service = Service::findOrFail($id);
        $service->update(['is_active' => !$service->is_active]);

        $status = $service->is_active ? 'activat' : 'dezactivat';
        return back()->with('success', "Serviciul a fost {$status}.");
    }

    /**
     * Toggle featured status for a craftsman (Recomandat/Top).
     */
    public function toggleCraftsmanFeatured($id)
    {
        $craftsman = User::where('role', 'specialist')->findOrFail($id);
        $craftsman->update(['is_featured' => !$craftsman->is_featured]);

        $status = $craftsman->is_featured ? 'adăugat la' : 'eliminat din';
        return back()->with('success', "Meseriașul a fost {$status} lista de recomandați.");
    }

    /**
     * Toggle verified status for a craftsman.
     */
    public function toggleCraftsmanVerified($id)
    {
        $craftsman = User::where('role', 'specialist')->findOrFail($id);
        $craftsman->update(['is_verified' => !$craftsman->is_verified]);

        $status = $craftsman->is_verified ? 'verificat' : 'neverificat';
        return back()->with('success', "Meseriașul a fost marcat ca {$status}.");
    }

    /**
     * Show craftsman edit page.
     */
    public function editCraftsman($id)
    {
        $craftsman = User::where('role', 'specialist')
            ->with(['category', 'location', 'services', 'reviews', 'gallery',
                    'subscriptions' => fn($q) => $q->with('plan')->orderByDesc('created_at')])
            ->withCount(['reviews', 'gallery', 'services'])
            ->findOrFail($id);

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $locations = \App\Models\Location::where('is_active', true)->orderBy('city')->get();
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        $activeSubscription = $craftsman->subscriptions
            ->first(fn($s) => in_array($s->status, ['active', 'trial']) && ($s->ends_at === null || $s->ends_at->isFuture()));

        return view('admin.craftsmen.edit', compact('craftsman', 'categories', 'locations', 'plans', 'activeSubscription'));
    }

    /**
     * Update craftsman from admin.
     */
    public function updateCraftsman(Request $request, $id)
    {
        $craftsman = User::where('role', 'specialist')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'category_id' => 'nullable|exists:categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'specialization' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Sanitizare descriere (permite doar taguri HTML sigure)
        if (!empty($validated['description'])) {
            $validated['description'] = strip_tags($validated['description'],
                '<p><br><strong><b><em><i><ul><ol><li><h2><h3><a><blockquote>');
        }

        $craftsman->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $craftsman->phone,
            'category_id' => $validated['category_id'],
            'location_id' => $validated['location_id'],
            'specialization' => $validated['specialization'] ?? $craftsman->specialization,
            'experience_years' => $validated['experience_years'] ?? $craftsman->experience_years,
            'description' => $validated['description'] ?? $craftsman->description,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
            'is_verified' => $request->has('is_verified'),
            'available_weekends' => $request->has('available_weekends'),
            'emergency_services' => $request->has('emergency_services'),
            'has_insurance' => $request->has('has_insurance'),
        ]);

        return redirect()->route('admin.craftsmen.edit', $craftsman->id)
            ->with('success', 'Datele meșerișului au fost actualizate.');
    }

    /**
     * Assign / update subscription for a craftsman from admin.
     */
    public function assignSubscription(Request $request, $id)
    {
        $craftsman = User::where('role', 'specialist')->findOrFail($id);

        $validated = $request->validate([
            'plan_id'            => 'required|exists:plans,id',
            'status'             => 'required|in:active,trial,cancelled',
            'ends_at'            => 'nullable|date|after:today',
            'payment_reference'  => 'nullable|string|max:255',
        ]);

        // Anulează orice subscripție activă anterioară
        Subscription::where('user_id', $craftsman->id)
            ->whereIn('status', ['active', 'trial'])
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        Subscription::create([
            'user_id'           => $craftsman->id,
            'plan_id'           => $validated['plan_id'],
            'status'            => $validated['status'],
            'started_at'        => now(),
            'ends_at'           => $validated['ends_at'] ? Carbon::parse($validated['ends_at'])->endOfDay() : null,
            'payment_provider'  => 'manual',
            'payment_reference' => $validated['payment_reference'] ?? 'Admin: ' . auth()->user()->name,
        ]);

        $plan = Plan::find($validated['plan_id']);

        return redirect()->route('admin.craftsmen.edit', $craftsman->id)
            ->with('success', 'Planul "' . $plan->name . '" a fost activat pentru ' . $craftsman->name . '.');
    }

    /**
     * Cancel active subscription for a craftsman.
     */
    public function cancelSubscription($id)
    {
        $craftsman = User::where('role', 'specialist')->findOrFail($id);

        Subscription::where('user_id', $craftsman->id)
            ->whereIn('status', ['active', 'trial'])
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return redirect()->route('admin.craftsmen.edit', $craftsman->id)
            ->with('success', 'Subscripția a fost anulată.');
    }
}
