<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Location;
use App\Models\PublicJobRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * Regressie pentru bug-ul din 22 iulie 2026: variabila 'message' din datele
 * trimise catre Mail::send() era suprascrisa de Laravel cu obiectul intern
 * Illuminate\Mail\Message, iar randarea sablonului pica la htmlspecialchars().
 * Eroarea era prinsa silentios de try/catch, fara nicio urma vizibila.
 */
class CraftsmanRespondMailRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_marking_interested_sends_client_email_without_logging_a_warning(): void
    {
        Log::spy();

        $category = Category::factory()->create();
        $location = Location::factory()->create();

        $craftsman = User::factory()->create([
            'role' => 'specialist',
            'is_active' => true,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'phone' => '0740000000',
            'slug' => 'craftsman-' . uniqid(),
        ]);

        $jobRequest = PublicJobRequest::create([
            'category_id' => $category->id,
            'location_id' => $location->id,
            'name' => 'Maria Vasile',
            'phone' => '0740000000',
            'email' => 'maria@example.test',
            'title' => 'Montaj centrala',
            'description' => str_repeat('a', 25),
            'urgency' => 'flexible',
        ]);

        $response = $this->actingAs($craftsman)->post(
            route('craftsman.public-requests.respond', $jobRequest),
            [
                'action' => 'interested',
                'message' => 'Pot veni mâine după ora 10.',
            ]
        );

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('public_job_request_responses', [
            'public_job_request_id' => $jobRequest->id,
            'craftsman_id' => $craftsman->id,
            'action' => 'interested',
        ]);

        Log::shouldNotHaveReceived('warning');
    }
}
