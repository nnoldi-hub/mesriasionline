<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_route_is_throttled_after_configured_attempts(): void
    {
        Cache::flush();

        $client = User::factory()->create(['role' => 'client', 'is_active' => true]);
        $this->actingAs($client);

        // Config pentru 'search' in CustomRateLimiter: 30 incercari / 60s.
        for ($i = 0; $i < 30; $i++) {
            $response = $this->get(route('client.search'));
            $response->assertOk();
        }

        $this->get(route('client.search'))->assertStatus(429);
    }
}
