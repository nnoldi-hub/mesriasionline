?php

use App\Http\Controllers\Api\CraftsmenApiController;
use App\Http\Controllers\Api\CategoriesApiController;
use App\Http\Controllers\Api\LocationsApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API v1
Route::prefix('v1')->group(function () {
    
    // Public endpoints (no authentication required)
    Route::middleware(['throttle:api'])->group(function () {
        
        // Craftsmen
        Route::get('/craftsmen', [CraftsmenApiController::class, 'index'])
            ->name('api.craftsmen.index');
        Route::get('/craftsmen/filter', [CraftsmenApiController::class, 'filter'])
            ->name('api.craftsmen.filter');
        Route::get('/craftsmen/{identifier}', [CraftsmenApiController::class, 'show'])
            ->name('api.craftsmen.show');
        Route::get('/craftsmen/{identifier}/reviews', [CraftsmenApiController::class, 'reviews'])
            ->name('api.craftsmen.reviews');
        Route::get('/craftsmen/{identifier}/services', [CraftsmenApiController::class, 'services'])
            ->name('api.craftsmen.services');
        
        // Categories
        Route::get('/categories', [CategoriesApiController::class, 'index'])
            ->name('api.categories.index');
        Route::get('/categories/{identifier}', [CategoriesApiController::class, 'show'])
            ->name('api.categories.show');
        
        // Locations
        Route::get('/locations', [LocationsApiController::class, 'index'])
            ->name('api.locations.index');
        Route::get('/locations/{id}', [LocationsApiController::class, 'show'])
            ->name('api.locations.show');
        
        // Stats
        Route::get('/stats', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'craftsmen_count' => \App\Models\User::where('role', 'craftsman')
                        ->where('status', 'active')
                        ->count(),
                    'categories_count' => \App\Models\Category::where('is_active', true)->count(),
                    'reviews_count' => \App\Models\Review::where('status', 'approved')->count(),
                    'cities_covered' => \App\Models\Location::distinct('city')->count('city'),
                ],
            ]);
        })->name('api.stats');
    });
    
    // Authenticated endpoints
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        
        // Current user
        Route::get('/user', function (Request $request) {
            return response()->json([
                'success' => true,
                'data' => $request->user(),
            ]);
        })->name('api.user');
        
        // Favorites
        Route::get('/favorites', function (Request $request) {
            $favorites = $request->user()->favorites()
                ->with('craftsman')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $favorites,
            ]);
        })->name('api.favorites.index');
        
        Route::post('/favorites/{craftsman}', function (Request $request, $craftsman) {
            $result = \App\Models\Favorite::toggle($request->user()->id, $craftsman);
            
            return response()->json([
                'success' => true,
                'favorited' => $result,
            ]);
        })->name('api.favorites.toggle');
        
        // Messages (basic)
        Route::get('/conversations', function (Request $request) {
            $conversations = $request->user()->conversations()
                ->with(['lastMessage', 'otherParticipant'])
                ->orderByDesc('updated_at')
                ->paginate(20);
            
            return response()->json([
                'success' => true,
                'data' => $conversations,
            ]);
        })->name('api.conversations.index');
        
        // Quote requests (for craftsmen)
        Route::get('/quote-requests', function (Request $request) {
            if ($request->user()->role !== 'craftsman') {
                return response()->json([
                    'success' => false,
                    'message' => 'Acces interzis.',
                ], 403);
            }
            
            $requests = $request->user()->quoteRequests()
                ->with('client')
                ->orderByDesc('created_at')
                ->paginate(20);
            
            return response()->json([
                'success' => true,
                'data' => $requests,
            ]);
        })->name('api.quote-requests.index');
    });
});

// API info
Route::get('/', function () {
    return response()->json([
        'name' => 'Omul Potrivit API',
        'version' => '1.0',
        'documentation' => url('/api/docs'),
        'endpoints' => [
            'craftsmen' => url('/api/v1/craftsmen'),
            'categories' => url('/api/v1/categories'),
            'locations' => url('/api/v1/locations'),
            'stats' => url('/api/v1/stats'),
        ],
    ]);
})->name('api.info');
