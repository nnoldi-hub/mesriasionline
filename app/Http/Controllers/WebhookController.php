<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use App\Models\WebhookDelivery;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    protected WebhookService $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Display all webhooks for authenticated user.
     */
    public function index()
    {
        $webhooks = auth()->user()
            ->webhooks()
            ->withCount(['deliveries', 'deliveries as successful_deliveries_count' => function ($query) {
                $query->where('success', true);
            }])
            ->latest()
            ->paginate(20);

        $availableEvents = Webhook::getAvailableEvents();

        return view('webhooks.index', compact('webhooks', 'availableEvents'));
    }

    /**
     * Show create webhook form.
     */
    public function create()
    {
        $availableEvents = Webhook::getAvailableEvents();
        return view('webhooks.create', compact('availableEvents'));
    }

    /**
     * Store new webhook.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'events' => 'required|array|min:1',
            'events.*' => 'string|in:' . implode(',', array_keys(Webhook::getAvailableEvents())),
            'secret' => 'nullable|string|min:16|max:255',
        ]);

        // Generate secret if not provided
        if (empty($validated['secret'])) {
            $validated['secret'] = Str::random(32);
        }

        $webhook = auth()->user()->webhooks()->create($validated);

        return redirect()
            ->route('webhooks.show', $webhook)
            ->with('success', 'Webhook a fost creat cu succes! Salvați secret-ul pentru validarea semnăturilor.');
    }

    /**
     * Show webhook details and delivery history.
     */
    public function show(Webhook $webhook)
    {
        $this->authorize('view', $webhook);

        $webhook->load('deliveries');
        
        $deliveries = $webhook->deliveries()
            ->latest()
            ->paginate(20);

        $statistics = $this->webhookService->getStatistics($webhook);

        return view('webhooks.show', compact('webhook', 'deliveries', 'statistics'));
    }

    /**
     * Show edit webhook form.
     */
    public function edit(Webhook $webhook)
    {
        $this->authorize('update', $webhook);

        $availableEvents = Webhook::getAvailableEvents();
        return view('webhooks.edit', compact('webhook', 'availableEvents'));
    }

    /**
     * Update webhook.
     */
    public function update(Request $request, Webhook $webhook)
    {
        $this->authorize('update', $webhook);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'events' => 'required|array|min:1',
            'events.*' => 'string|in:' . implode(',', array_keys(Webhook::getAvailableEvents())),
            'is_active' => 'boolean',
        ]);

        $webhook->update($validated);

        return redirect()
            ->route('webhooks.show', $webhook)
            ->with('success', 'Webhook actualizat cu succes.');
    }

    /**
     * Delete webhook.
     */
    public function destroy(Webhook $webhook)
    {
        $this->authorize('delete', $webhook);

        $webhook->delete();

        return redirect()
            ->route('webhooks.index')
            ->with('success', 'Webhook șters cu succes.');
    }

    /**
     * Test webhook endpoint.
     */
    public function test(Webhook $webhook)
    {
        $this->authorize('view', $webhook);

        $result = $this->webhookService->test($webhook);

        if ($result['success']) {
            return back()->with('success', "Webhook testat cu succes! Status: {$result['status']}");
        } else {
            return back()->with('error', "Test eșuat: " . ($result['error'] ?? 'Unknown error'));
        }
    }

    /**
     * Retry failed delivery.
     */
    public function retryDelivery(WebhookDelivery $delivery)
    {
        $this->authorize('view', $delivery->webhook);

        $result = $this->webhookService->retry($delivery);

        if ($result->success) {
            return back()->with('success', 'Livrare reîncercată cu succes!');
        } else {
            return back()->with('error', 'Reîncercare eșuată: ' . $result->error_message);
        }
    }

    /**
     * Toggle webhook active status.
     */
    public function toggleActive(Webhook $webhook)
    {
        $this->authorize('update', $webhook);

        $webhook->update(['is_active' => !$webhook->is_active]);

        $status = $webhook->is_active ? 'activat' : 'dezactivat';
        return back()->with('success', "Webhook {$status} cu succes.");
    }

    /**
     * Regenerate webhook secret.
     */
    public function regenerateSecret(Webhook $webhook)
    {
        $this->authorize('update', $webhook);

        $newSecret = Str::random(32);
        $webhook->update(['secret' => $newSecret]);

        return back()->with([
            'success' => 'Secret regenerat cu succes.',
            'new_secret' => $newSecret,
        ]);
    }
}
