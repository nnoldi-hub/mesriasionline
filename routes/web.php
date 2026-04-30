
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ClientRegisterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\SubscriptionManagementController as AdminSubscriptionController;
use App\Http\Controllers\Craftsman\DashboardController as CraftsmanDashboardController;
use App\Http\Controllers\Craftsman\QuoteController as CraftsmanQuoteController;
use App\Http\Controllers\Craftsman\AnalyticsController as CraftsmanAnalyticsController;
use App\Http\Controllers\Craftsman\AvailabilityController as CraftsmanAvailabilityController;
use App\Http\Controllers\Craftsman\CertificationController as CraftsmanCertificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\LandingController;

// ─── Chatbot AI (rate limit: 20 cereri/minut per IP) ─────────────────────────
Route::middleware('throttle:20,1')->group(function () {
    Route::post('/api/chatbot',         [ChatbotController::class, 'chat'])->name('chatbot.chat');
    Route::post('/api/chatbot/reset',   [ChatbotController::class, 'reset'])->name('chatbot.reset');
    Route::post('/api/chatbot/convert', [ChatbotController::class, 'convert'])->name('chatbot.convert');
});

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/meserias/{slug}', [HomeController::class, 'show'])->name('craftsman.show');
Route::get('/api/craftsmen/nearby', [HomeController::class, 'nearby'])->name('craftsmen.nearby');

// Compare Craftsmen
Route::get('/meseriasi/compara', [\App\Http\Controllers\CompareController::class, 'index'])->name('craftsmen.compare');
Route::get('/api/craftsmen/compare-data', [\App\Http\Controllers\CompareController::class, 'getData'])->name('api.craftsmen.compare');
Route::get('/despre', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/termeni-si-conditii', [PageController::class, 'terms'])->name('terms');

// SEO Routes
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

// Landing pages per meserie (SEO)
Route::get('/meseriasi/{categorySlug}/{locationSlug}', [LandingController::class, 'categoryCity'])->name('landing.category-city');
Route::get('/meseriasi/{categorySlug}', [LandingController::class, 'category'])->name('landing.category');
Route::get('/sitemap-meserii.xml', [LandingController::class, 'sitemap'])->name('landing.sitemap');

// Politica cookies
Route::view('/cookies', 'pages.cookies')->name('cookies');
Route::get('/politica-de-confidentialitate', [PageController::class, 'privacy'])->name('privacy');

// Cereri publice (fără cont)
use App\Http\Controllers\PublicJobRequestController;
Route::get('/cere-oferte', [PublicJobRequestController::class, 'create'])->name('public-request.create');
Route::post('/cere-oferte', [PublicJobRequestController::class, 'store'])->name('public-request.store');
Route::get('/cere-oferte/confirmare/{token}', [PublicJobRequestController::class, 'success'])->name('public-request.success');

// Articles & Blog
Route::get('/articole', [ArticleController::class, 'index'])->name('articole');
Route::get('/articole/{slug}', [ArticleController::class, 'show'])->name('articole.show');
Route::get('/interviuri', [ArticleController::class, 'interviews'])->name('interviuri');

// Q&A - Întrebări și răspunsuri
Route::get('/intrebari', [ArticleController::class, 'questions'])->name('intrebari');
Route::get('/intrebari/pune-o-intrebare', [ArticleController::class, 'askQuestion'])->name('intrebari.pune');
Route::post('/intrebari', [ArticleController::class, 'storeQuestion'])->name('intrebari.store');


// Formular generic pentru solicitare mentenanță/întreținere
Route::get('/solicitare-serviciu', function() {
    return view('services.request');
})->name('service.request');
Route::post('/solicitare-serviciu', [\App\Http\Controllers\ServiceBookingController::class, 'submitGenericRequest'])->name('service.request.submit');

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Registration routes - Craftsman
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Registration routes - Client
Route::get('/register/client', [ClientRegisterController::class, 'showRegisterForm'])->name('register.client.form');
Route::post('/register/client', [ClientRegisterController::class, 'register'])->name('register.client');

// Landing page pentru meseriași
Route::get('/pentru-meseriasi', function () {
    return view('pages.pentru-meseriasi');
})->name('pentru-meseriasi');

// Planuri & Subscriptions
Route::get('/planuri', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('plans.index');
Route::middleware(['auth'])->group(function () {
    // Free plan downgrade (no payment needed)
    Route::post('/subscribe/{slug}', [\App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::post('/subscription/cancel', [\App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('subscription.cancel');
});

// Stripe Checkout (auth required)
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout/{slug}', [\App\Http\Controllers\PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/checkout/{slug}/stripe', [\App\Http\Controllers\PaymentController::class, 'redirectToStripe'])->name('payment.stripe');
    Route::get('/payment/success', [\App\Http\Controllers\PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [\App\Http\Controllers\PaymentController::class, 'cancel'])->name('payment.cancel');
});

// Stripe Webhook (no CSRF, no auth)
Route::post('/stripe/webhook', [\App\Http\Controllers\StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Onboarding wizard — înregistrare rapidă
Route::get('/inregistrare', [\App\Http\Controllers\OnboardingController::class, 'showQuickRegister'])->name('onboarding.quick-register');
Route::post('/inregistrare', [\App\Http\Controllers\OnboardingController::class, 'quickRegister'])->name('onboarding.quick-register.submit');

// Onboarding wizard — pași (necesită autentificare)
Route::middleware(['auth', 'specialist'])->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/{step}', [\App\Http\Controllers\OnboardingController::class, 'showStep'])
        ->where('step', '[1-4]')
        ->name('step');
    Route::put('/save/{step}', [\App\Http\Controllers\OnboardingController::class, 'saveStep'])
        ->where('step', '[1-4]')
        ->name('save');
});

// Affiliate routes
Route::middleware(['auth'])->prefix('affiliate')->name('affiliate.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AffiliateController::class, 'index'])->name('dashboard');
    Route::post('/register', [\App\Http\Controllers\AffiliateController::class, 'register'])->name('register');
    Route::get('/links', [\App\Http\Controllers\AffiliateController::class, 'links'])->name('links');
    Route::get('/referrals', [\App\Http\Controllers\AffiliateController::class, 'referrals'])->name('referrals');
    Route::get('/earnings', [\App\Http\Controllers\AffiliateController::class, 'earnings'])->name('earnings');
    Route::get('/payouts', [\App\Http\Controllers\AffiliateController::class, 'payouts'])->name('payouts');
    Route::post('/request-payout', [\App\Http\Controllers\AffiliateController::class, 'requestPayout'])->name('request-payout');
    Route::put('/payment-settings', [\App\Http\Controllers\AffiliateController::class, 'updatePaymentSettings'])->name('payment-settings');
});

// Admin routes
Route::middleware(['auth', App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [AdminDashboardController::class, 'editProfile'])->name('profile');
    Route::post('/profil', [AdminDashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Craftsmen management
    Route::get('/craftsmen', [AdminDashboardController::class, 'craftsmen'])->name('craftsmen');
    Route::get('/craftsmen/{id}/edit', [AdminDashboardController::class, 'editCraftsman'])->name('craftsmen.edit');
    Route::put('/craftsmen/{id}', [AdminDashboardController::class, 'updateCraftsman'])->name('craftsmen.update');
    Route::post('/craftsmen/{id}/toggle-status', [AdminDashboardController::class, 'toggleCraftsmanStatus'])->name('craftsmen.toggle-status');
    Route::post('/craftsmen/{id}/toggle-featured', [AdminDashboardController::class, 'toggleCraftsmanFeatured'])->name('craftsmen.toggle-featured');
    Route::post('/craftsmen/{id}/toggle-verified', [AdminDashboardController::class, 'toggleCraftsmanVerified'])->name('craftsmen.toggle-verified');
    Route::post('/craftsmen/{id}/subscription', [AdminDashboardController::class, 'assignSubscription'])->name('craftsmen.subscription.assign');
    Route::post('/craftsmen/{id}/subscription/cancel', [AdminDashboardController::class, 'cancelSubscription'])->name('craftsmen.subscription.cancel');
    
    Route::get('/reviews', [AdminDashboardController::class, 'reviews'])->name('reviews');
    Route::post('/reviews/{id}/approve', [AdminDashboardController::class, 'approveReview'])->name('reviews.approve');
    // Listare solicitări generice mentenanță/întreținere
    Route::get('/generic-requests', [AdminDashboardController::class, 'genericRequests'])->name('generic.requests');
    Route::post('/generic-requests/{id}/complete', [AdminDashboardController::class, 'completeGenericRequest'])->name('generic.requests.complete');
    // Services management
    Route::get('/services', [AdminDashboardController::class, 'services'])->name('services');
    Route::get('/services/{id}/edit', [AdminDashboardController::class, 'editService'])->name('services.edit');
    Route::put('/services/{id}', [AdminDashboardController::class, 'updateService'])->name('services.update');
    Route::post('/services/{id}/toggle-status', [AdminDashboardController::class, 'toggleServiceStatus'])->name('services.toggle-status');
    
    // Articles management
    Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/create', [AdminArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [AdminArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{id}/edit', [AdminArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{id}', [AdminArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{id}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');
    
    // Questions management
    Route::get('/questions', [AdminArticleController::class, 'questions'])->name('articles.questions');
    Route::get('/questions/{id}/answer', [AdminArticleController::class, 'answerQuestion'])->name('articles.answer-question');
    Route::post('/questions/{id}/answer', [AdminArticleController::class, 'storeAnswer'])->name('articles.store-answer');
    Route::patch('/questions/{id}/status', [AdminArticleController::class, 'updateQuestionStatus'])->name('articles.update-question-status');
    Route::patch('/questions/{id}/toggle-featured', [AdminArticleController::class, 'toggleQuestionFeatured'])->name('articles.toggle-question-featured');
    Route::delete('/questions/{id}', [AdminArticleController::class, 'deleteQuestion'])->name('articles.delete-question');
    
    // Affiliate Management
    Route::prefix('affiliates')->name('affiliates.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AffiliateManagementController::class, 'index'])->name('index');
        Route::get('/list', [\App\Http\Controllers\Admin\AffiliateManagementController::class, 'affiliates'])->name('list');
        Route::get('/{affiliate}', [\App\Http\Controllers\Admin\AffiliateManagementController::class, 'showAffiliate'])->name('show');
        Route::post('/{affiliate}/approve', [\App\Http\Controllers\Admin\AffiliateManagementController::class, 'approveAffiliate'])->name('approve');
        Route::post('/{affiliate}/reject', [\App\Http\Controllers\Admin\AffiliateManagementController::class, 'rejectAffiliate'])->name('reject');
        Route::post('/{affiliate}/suspend', [\App\Http\Controllers\Admin\AffiliateManagementController::class, 'suspendAffiliate'])->name('suspend');
        Route::get('/payouts/list', [\App\Http\Controllers\Admin\AffiliateManagementController::class, 'payouts'])->name('payouts');
        Route::post('/payouts/{payout}/process', [\App\Http\Controllers\Admin\AffiliateManagementController::class, 'processPayout'])->name('payouts.process');
        Route::post('/payouts/{payout}/complete', [\App\Http\Controllers\Admin\AffiliateManagementController::class, 'completePayout'])->name('payouts.complete');
        Route::post('/payouts/{payout}/fail', [\App\Http\Controllers\Admin\AffiliateManagementController::class, 'failPayout'])->name('payouts.fail');
        Route::get('/commissions/list', [\App\Http\Controllers\Admin\AffiliateManagementController::class, 'commissions'])->name('commissions');
        Route::post('/commissions/{commission}/approve', [\App\Http\Controllers\Admin\AffiliateManagementController::class, 'approveCommission'])->name('commissions.approve');
        Route::post('/commissions/{commission}/reject', [\App\Http\Controllers\Admin\AffiliateManagementController::class, 'rejectCommission'])->name('commissions.reject');
        Route::get('/programs/list', [\App\Http\Controllers\Admin\AffiliateManagementController::class, 'programs'])->name('programs');
        Route::post('/programs', [\App\Http\Controllers\Admin\AffiliateManagementController::class, 'storeProgram'])->name('programs.store');
        Route::put('/programs/{program}', [\App\Http\Controllers\Admin\AffiliateManagementController::class, 'updateProgram'])->name('programs.update');
    });
    
    // Analytics & Reports
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('index');
        Route::get('/funnel', [\App\Http\Controllers\Admin\AnalyticsController::class, 'funnel'])->name('funnel');
        Route::get('/traffic', [\App\Http\Controllers\Admin\AnalyticsController::class, 'traffic'])->name('traffic');
        Route::get('/users', [\App\Http\Controllers\Admin\AnalyticsController::class, 'users'])->name('users');
        Route::post('/export-pdf', [\App\Http\Controllers\Admin\AnalyticsController::class, 'exportPdf'])->name('export-pdf');
        Route::post('/export-excel', [\App\Http\Controllers\Admin\AnalyticsController::class, 'exportExcel'])->name('export-excel');
    });

    // Chatbot AI — Monitorizare
    Route::prefix('chatbot')->name('chatbot.')->group(function () {
        Route::get('/',                   [\App\Http\Controllers\Admin\ChatbotAdminController::class, 'index'])->name('index');

        // Knowledge Base — Antrenare chatbot (trebuie înainte de /{conversation})
        Route::prefix('knowledge')->name('knowledge.')->group(function () {
            Route::get('/',                    [\App\Http\Controllers\Admin\ChatbotKnowledgeController::class, 'index'])->name('index');
            Route::get('/create',              [\App\Http\Controllers\Admin\ChatbotKnowledgeController::class, 'create'])->name('create');
            Route::post('/',                   [\App\Http\Controllers\Admin\ChatbotKnowledgeController::class, 'store'])->name('store');
            Route::get('/test',                [\App\Http\Controllers\Admin\ChatbotKnowledgeController::class, 'test'])->name('test');
            Route::post('/test',               [\App\Http\Controllers\Admin\ChatbotKnowledgeController::class, 'testQuery'])->name('test.query');
            Route::get('/{knowledge}/edit',    [\App\Http\Controllers\Admin\ChatbotKnowledgeController::class, 'edit'])->name('edit');
            Route::put('/{knowledge}',         [\App\Http\Controllers\Admin\ChatbotKnowledgeController::class, 'update'])->name('update');
            Route::delete('/{knowledge}',      [\App\Http\Controllers\Admin\ChatbotKnowledgeController::class, 'destroy'])->name('destroy');
            Route::patch('/{knowledge}/toggle',[\App\Http\Controllers\Admin\ChatbotKnowledgeController::class, 'toggleActive'])->name('toggle');
        });

        Route::get('/{conversation}',     [\App\Http\Controllers\Admin\ChatbotAdminController::class, 'show'])->name('show');
        Route::delete('/{conversation}',  [\App\Http\Controllers\Admin\ChatbotAdminController::class, 'destroy'])->name('destroy');
    });
    
    // Email Templates management
    Route::prefix('email-templates')->name('email-templates.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'store'])->name('store');
        Route::get('/{emailTemplate}/edit', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'edit'])->name('edit');
        Route::put('/{emailTemplate}', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'update'])->name('update');
        Route::delete('/{emailTemplate}', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'destroy'])->name('destroy');
        Route::get('/{emailTemplate}/preview', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'preview'])->name('preview');
        Route::post('/preview-live', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'previewLive'])->name('preview-live');
        Route::post('/{emailTemplate}/toggle-status', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{emailTemplate}/set-default', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'setDefault'])->name('set-default');
        Route::post('/{emailTemplate}/duplicate', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'duplicate'])->name('duplicate');
        Route::post('/seed-defaults', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'seedDefaults'])->name('seed-defaults');
    });
    
    // Subscriptions & Payments management
    Route::get('/subscriptions', [AdminSubscriptionController::class, 'subscriptions'])->name('subscriptions');
    Route::get('/transactions', [AdminSubscriptionController::class, 'transactions'])->name('transactions');

    // Notification Settings
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/settings', [\App\Http\Controllers\Admin\NotificationSettingsController::class, 'index'])->name('settings');
        Route::put('/settings', [\App\Http\Controllers\Admin\NotificationSettingsController::class, 'update'])->name('settings.update');
        Route::post('/test-email', [\App\Http\Controllers\Admin\NotificationSettingsController::class, 'testEmail'])->name('test-email');
    });

    // Platform Settings (social media, contact info)
    Route::get('/platform-settings', [\App\Http\Controllers\Admin\PlatformSettingsController::class, 'index'])->name('platform-settings');
    Route::put('/platform-settings', [\App\Http\Controllers\Admin\PlatformSettingsController::class, 'update'])->name('platform-settings.update');

    // Cereri publice clienți (formularul /cere-oferte)
    Route::get('/cereri-publice', [AdminDashboardController::class, 'publicJobRequests'])->name('public-job-requests.index');
    Route::get('/cereri-publice/{jobRequest}', [AdminDashboardController::class, 'publicJobRequestShow'])->name('public-job-requests.show');
    Route::patch('/cereri-publice/{jobRequest}/toggle-status', [AdminDashboardController::class, 'publicJobRequestToggleStatus'])->name('public-job-requests.toggle-status');
});

// Craftsman routes
Route::middleware(['auth', App\Http\Middleware\SpecialistMiddleware::class, 'onboarding'])->prefix('craftsman')->name('craftsman.')->group(function () {
    Route::get('/dashboard', [CraftsmanDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [CraftsmanDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [CraftsmanDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/services', [CraftsmanDashboardController::class, 'services'])->name('services');
    Route::get('/services/create', [CraftsmanDashboardController::class, 'createService'])->name('services.create');
    Route::post('/services', [CraftsmanDashboardController::class, 'storeService'])->name('services.store');
    Route::get('/services/{id}/edit', [CraftsmanDashboardController::class, 'editService'])->name('services.edit');
    Route::put('/services/{id}', [CraftsmanDashboardController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{id}', [CraftsmanDashboardController::class, 'deleteService'])->name('services.delete');
    Route::post('/services/{id}/toggle-status', [CraftsmanDashboardController::class, 'toggleServiceStatus'])->name('services.toggle-status');
    
    // Gallery
    Route::get('/gallery', [CraftsmanDashboardController::class, 'gallery'])->name('gallery');
    Route::get('/gallery/upload', [CraftsmanDashboardController::class, 'uploadGallery'])->name('gallery.upload');
    Route::post('/gallery', [CraftsmanDashboardController::class, 'storeGallery'])->name('gallery.store');
    Route::get('/gallery/{id}/edit', [CraftsmanDashboardController::class, 'editGalleryImage'])->name('gallery.edit');
    Route::put('/gallery/{id}', [CraftsmanDashboardController::class, 'updateGalleryImage'])->name('gallery.update');
    Route::delete('/gallery/{id}', [CraftsmanDashboardController::class, 'deleteGalleryImage'])->name('gallery.delete');
    Route::patch('/gallery/{id}/toggle-featured', [CraftsmanDashboardController::class, 'toggleFeaturedImage'])->name('gallery.toggle-featured');
    
    // Social Media
    Route::get('/social-media', [CraftsmanDashboardController::class, 'socialMedia'])->name('social-media');
    Route::put('/social-media', [CraftsmanDashboardController::class, 'updateSocialMedia'])->name('social-media.update');
    
    Route::get('/appointments', [CraftsmanDashboardController::class, 'appointments'])->name('appointments');
    Route::get('/reviews', [CraftsmanDashboardController::class, 'reviews'])->name('reviews');
    
    // Quotes - Cereri de ofertă primite
    Route::get('/quotes', [CraftsmanQuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/pending-count', [CraftsmanQuoteController::class, 'pendingCount'])->name('quotes.pending-count');
    Route::get('/quotes/{quoteRequest}', [CraftsmanQuoteController::class, 'show'])->name('quotes.show');
    Route::post('/quotes/{quoteRequest}', [CraftsmanQuoteController::class, 'store'])->name('quotes.store')->middleware('plan.limits');
    Route::get('/quotes/{quoteRequest}/edit/{quote}', [CraftsmanQuoteController::class, 'edit'])->name('quotes.edit');
    Route::put('/quotes/{quoteRequest}/update/{quote}', [CraftsmanQuoteController::class, 'update'])->name('quotes.update');
    Route::post('/quotes/{quoteRequest}/withdraw/{quote}', [CraftsmanQuoteController::class, 'withdraw'])->name('quotes.withdraw');
    
    // Analytics & Rapoarte
    Route::get('/analytics', [CraftsmanAnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/export', [CraftsmanAnalyticsController::class, 'export'])->name('analytics.export');
    
    // Disponibilitate & Booking
    Route::get('/availability', [CraftsmanAvailabilityController::class, 'index'])->name('availability');
    Route::put('/availability/schedule', [CraftsmanAvailabilityController::class, 'updateSchedule'])->name('availability.schedule');
    Route::post('/availability/generate', [CraftsmanAvailabilityController::class, 'generateSlots'])->name('availability.generate');
    Route::post('/availability/slots/{slot}/block', [CraftsmanAvailabilityController::class, 'blockSlot'])->name('availability.block-slot');
    Route::post('/availability/slots/{slot}/release', [CraftsmanAvailabilityController::class, 'releaseSlot'])->name('availability.release-slot');
    Route::post('/availability/vacation', [CraftsmanAvailabilityController::class, 'addVacation'])->name('availability.add-vacation');
    Route::delete('/availability/vacation', [CraftsmanAvailabilityController::class, 'removeVacation'])->name('availability.remove-vacation');
    Route::put('/availability/booking-settings', [CraftsmanAvailabilityController::class, 'updateBookingSettings'])->name('availability.booking-settings');
    Route::get('/availability/slots', [CraftsmanAvailabilityController::class, 'getSlotsForDate'])->name('availability.slots');
    
    // Calendar Integration
    Route::get('/calendar/integration', [\App\Http\Controllers\Craftsman\CalendarIntegrationController::class, 'index'])->name('calendar.integration');
    Route::get('/calendar/google/connect', [\App\Http\Controllers\Craftsman\CalendarIntegrationController::class, 'googleConnect'])->name('calendar.google.connect');
    Route::get('/calendar/google/callback', [\App\Http\Controllers\Craftsman\CalendarIntegrationController::class, 'googleCallback'])->name('calendar.google.callback');
    Route::post('/calendar/google/disconnect', [\App\Http\Controllers\Craftsman\CalendarIntegrationController::class, 'googleDisconnect'])->name('calendar.google.disconnect');
    Route::put('/calendar/google/calendar', [\App\Http\Controllers\Craftsman\CalendarIntegrationController::class, 'updateGoogleCalendar'])->name('calendar.google.update');
    Route::post('/calendar/google/sync', [\App\Http\Controllers\Craftsman\CalendarIntegrationController::class, 'syncToGoogle'])->name('calendar.google.sync');
    Route::get('/calendar/outlook/connect', [\App\Http\Controllers\Craftsman\CalendarIntegrationController::class, 'outlookConnect'])->name('calendar.outlook.connect');
    Route::get('/calendar/outlook/callback', [\App\Http\Controllers\Craftsman\CalendarIntegrationController::class, 'outlookCallback'])->name('calendar.outlook.callback');
    Route::post('/calendar/outlook/disconnect', [\App\Http\Controllers\Craftsman\CalendarIntegrationController::class, 'outlookDisconnect'])->name('calendar.outlook.disconnect');
    
    // Certificări
    Route::get('/certifications', [CraftsmanCertificationController::class, 'index'])->name('certifications.index');
    Route::get('/certifications/create', [CraftsmanCertificationController::class, 'create'])->name('certifications.create');
    Route::post('/certifications', [CraftsmanCertificationController::class, 'store'])->name('certifications.store');
    Route::get('/certifications/{certification}/edit', [CraftsmanCertificationController::class, 'edit'])->name('certifications.edit');
    Route::put('/certifications/{certification}', [CraftsmanCertificationController::class, 'update'])->name('certifications.update');
    Route::delete('/certifications/{certification}', [CraftsmanCertificationController::class, 'destroy'])->name('certifications.destroy');

    // Cereri publice clienți (vizibile meseriașilor cu abonament)
    Route::get('/cereri-publice', [\App\Http\Controllers\Craftsman\PublicJobRequestController::class, 'index'])->name('public-requests.index');
    Route::get('/cereri-publice/{publicJobRequest}', [\App\Http\Controllers\Craftsman\PublicJobRequestController::class, 'show'])->name('public-requests.show');
    Route::post('/cereri-publice/{publicJobRequest}/raspunde', [\App\Http\Controllers\Craftsman\PublicJobRequestController::class, 'respond'])->name('public-requests.respond');
});

// Authenticated user routes (Messages, Notifications, Quotes)
Route::middleware(['auth'])->group(function () {
    // Reports - Export PDF/Excel
    Route::get('/reports/craftsman', [\App\Http\Controllers\ReportController::class, 'craftsmanReports'])->name('reports.craftsman');
    Route::post('/reports/craftsman/pdf', [\App\Http\Controllers\ReportController::class, 'exportCraftsmanPdf'])->name('reports.craftsman.pdf');
    Route::post('/reports/craftsman/excel', [\App\Http\Controllers\ReportController::class, 'exportCraftsmanExcel'])->name('reports.craftsman.excel');
    Route::get('/reports/client', [\App\Http\Controllers\ReportController::class, 'clientReports'])->name('reports.client');
    Route::post('/reports/client/pdf', [\App\Http\Controllers\ReportController::class, 'exportClientPdf'])->name('reports.client.pdf');
    Route::post('/reports/affiliate/pdf', [\App\Http\Controllers\ReportController::class, 'exportAffiliatePdf'])->name('reports.affiliate.pdf');
    
    // Messages
    Route::get('/mesaje', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/mesaje/nou', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/mesaje', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/mesaje/necitite', [MessageController::class, 'unreadCount'])->name('messages.unread-count');
    Route::get('/mesaje/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/mesaje/{conversation}/reply', [MessageController::class, 'reply'])->name('messages.reply');
    Route::post('/mesaje/{conversation}/archive', [MessageController::class, 'archive'])->name('messages.archive');
    
    // Notifications
    Route::get('/notificari', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notificari/necitite', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get('/notificari/recente', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::post('/notificari/{id}/citit', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');

    // Webhooks
    Route::resource('webhooks', \App\Http\Controllers\WebhookController::class);
    Route::post('/webhooks/{webhook}/test', [\App\Http\Controllers\WebhookController::class, 'test'])->name('webhooks.test');
    Route::post('/webhooks/{webhook}/toggle-active', [\App\Http\Controllers\WebhookController::class, 'toggleActive'])->name('webhooks.toggle-active');
    Route::post('/webhooks/{webhook}/regenerate-secret', [\App\Http\Controllers\WebhookController::class, 'regenerateSecret'])->name('webhooks.regenerate-secret');
    Route::post('/webhook-deliveries/{delivery}/retry', [\App\Http\Controllers\WebhookController::class, 'retryDelivery'])->name('webhook-deliveries.retry');
    Route::post('/notificari/citit-toate', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notificari/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notificari/citite/sterge', [NotificationController::class, 'destroyRead'])->name('notifications.destroy-read');
    
    // Push Subscriptions (WebPush)
    Route::post('/push-subscriptions', [\App\Http\Controllers\PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::delete('/push-subscriptions', [\App\Http\Controllers\PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');
    Route::get('/push-subscriptions/status', [\App\Http\Controllers\PushSubscriptionController::class, 'status'])->name('push.status');
    
    // Favorites
    Route::get('/favorite', [\App\Http\Controllers\FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorite/toggle', [\App\Http\Controllers\FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::delete('/favorite/{craftsman}', [\App\Http\Controllers\FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::put('/favorite/{craftsman}/notes', [\App\Http\Controllers\FavoriteController::class, 'updateNotes'])->name('favorites.notes');
    Route::get('/favorite/{craftsman}/check', [\App\Http\Controllers\FavoriteController::class, 'check'])->name('favorites.check');
    
    // Export & Import
    Route::get('/export/appointments', [\App\Http\Controllers\ExportController::class, 'exportAppointments'])->name('export.appointments');
    Route::get('/export/reviews', [\App\Http\Controllers\ExportController::class, 'exportReviews'])->name('export.reviews');
    Route::get('/export/craftsmen', [\App\Http\Controllers\ExportController::class, 'exportCraftsmen'])->name('export.craftsmen')->middleware('can:manage-users');
    
    // Import (admin only)
    Route::middleware('can:manage-users')->prefix('import')->name('import.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ImportController::class, 'index'])->name('index');
        Route::post('/craftsmen/preview', [\App\Http\Controllers\ImportController::class, 'previewCraftsmen'])->name('craftsmen.preview');
        Route::post('/craftsmen', [\App\Http\Controllers\ImportController::class, 'importCraftsmen'])->name('craftsmen');
        Route::post('/services', [\App\Http\Controllers\ImportController::class, 'importServices'])->name('services');
        Route::get('/templates/craftsmen', [\App\Http\Controllers\ImportController::class, 'downloadCraftsmenTemplate'])->name('templates.craftsmen');
        Route::get('/templates/services', [\App\Http\Controllers\ImportController::class, 'downloadServicesTemplate'])->name('templates.services');
    });
    
    // Map & Geolocation
    Route::prefix('api/map')->name('api.map.')->group(function () {
        Route::post('/geocode', [\App\Http\Controllers\MapController::class, 'geocode'])->name('geocode');
        Route::post('/reverse-geocode', [\App\Http\Controllers\MapController::class, 'reverseGeocode'])->name('reverse-geocode');
        Route::post('/distance', [\App\Http\Controllers\MapController::class, 'calculateDistance'])->name('distance');
        Route::get('/search-radius', [\App\Http\Controllers\MapController::class, 'searchRadius'])->name('search-radius');
        Route::get('/craftsmen-markers', [\App\Http\Controllers\MapController::class, 'getCraftsmenMarkers'])->name('craftsmen-markers');
    });
    
    // Security - Two Factor Authentication
    Route::get('/security/2fa', [\App\Http\Controllers\TwoFactorController::class, 'index'])->name('security.2fa');
    Route::post('/security/2fa/enable', [\App\Http\Controllers\TwoFactorController::class, 'enable'])->name('security.2fa.enable');
    Route::post('/security/2fa/confirm', [\App\Http\Controllers\TwoFactorController::class, 'confirm'])->name('security.2fa.confirm');
    Route::post('/security/2fa/disable', [\App\Http\Controllers\TwoFactorController::class, 'disable'])->name('security.2fa.disable');
    Route::post('/security/2fa/recovery-codes', [\App\Http\Controllers\TwoFactorController::class, 'regenerateRecoveryCodes'])->name('security.2fa.recovery');
    
    // User Sessions Management
    Route::get('/security/sessions', [\App\Http\Controllers\SessionController::class, 'index'])->name('security.sessions');
    Route::delete('/security/sessions/{session}', [\App\Http\Controllers\SessionController::class, 'destroy'])->name('security.sessions.destroy');
    Route::post('/security/sessions/logout-all', [\App\Http\Controllers\SessionController::class, 'logoutAll'])->name('security.sessions.logout-all');
    
    // Article Likes
    Route::post('/articole/{article}/like', [\App\Http\Controllers\ArticleLikeController::class, 'toggle'])->name('articles.like');
    Route::get('/articole/{article}/likes', [\App\Http\Controllers\ArticleLikeController::class, 'status'])->name('articles.like.status');
    
    // Quote Requests (Client)
    Route::get('/cereri-oferta', [QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/cereri-oferta/nou', [QuoteController::class, 'create'])->name('quotes.create');
    Route::post('/cereri-oferta', [QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/cereri-oferta/{quoteRequest}', [QuoteController::class, 'show'])->name('quotes.show');
    Route::post('/cereri-oferta/{quoteRequest}/anuleaza', [QuoteController::class, 'cancel'])->name('quotes.cancel');
    Route::post('/cereri-oferta/{quoteRequest}/finalizat', [QuoteController::class, 'complete'])->name('quotes.complete');
    Route::post('/cereri-oferta/{quoteRequest}/accept/{quote}', [QuoteController::class, 'acceptQuote'])->name('quotes.accept');
    Route::post('/cereri-oferta/{quoteRequest}/reject/{quote}', [QuoteController::class, 'rejectQuote'])->name('quotes.reject');
});

// Client routes
Route::middleware(['auth', App\Http\Middleware\ClientMiddleware::class])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\Client\DashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [App\Http\Controllers\Client\DashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Addresses
    Route::resource('addresses', App\Http\Controllers\Client\AddressController::class);
    Route::patch('/addresses/{address}/set-default', [App\Http\Controllers\Client\AddressController::class, 'setDefault'])->name('addresses.set-default');
    
    // Search craftsmen
    Route::get('/search', [App\Http\Controllers\Client\SearchController::class, 'index'])->name('search');
    
    // Quotes (placeholder routes - folosesc controller existent pentru quotes)
    Route::get('/quotes', [App\Http\Controllers\QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/{quoteRequest}', [App\Http\Controllers\QuoteController::class, 'show'])->name('quotes.show');
    
    // Appointments (placeholder)
    Route::get('/appointments', function() {
        return view('client.appointments.index');
    })->name('appointments.index');
    
    // Reviews (placeholder)
    Route::get('/reviews', function() {
        return view('client.reviews.index');
    })->name('reviews.index');
});

// Search History (authenticated)
Route::middleware(['auth'])->group(function () {
    Route::get('/search/history', [\App\Http\Controllers\SearchHistoryController::class, 'index'])->name('search.history');
    Route::post('/search/history', [\App\Http\Controllers\SearchHistoryController::class, 'store'])->name('search.history.store');
    Route::delete('/search/history', [\App\Http\Controllers\SearchHistoryController::class, 'destroy'])->name('search.history.clear');
    Route::delete('/search/history/{search}', [\App\Http\Controllers\SearchHistoryController::class, 'destroyOne'])->name('search.history.delete');
    Route::get('/search/suggestions', [\App\Http\Controllers\SearchHistoryController::class, 'suggestions'])->name('search.suggestions');
});

// Language Switcher (public)
Route::get('/lang/{locale}', [\App\Http\Controllers\LocaleController::class, 'switch'])->name('locale.switch');
Route::get('/api/locales', [\App\Http\Controllers\LocaleController::class, 'available'])->name('locale.available');

// Settings page (authenticated)
Route::middleware(['auth'])->group(function () {
    Route::get('/setari', function () {
        return view('settings.index');
    })->name('settings');
    
    Route::get('/security/two-factor', function () {
        return view('security.two-factor');
    })->name('security.two-factor');
});

