<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Location;
use App\Models\Plan;
use App\Models\PublicJobRequest;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\NewPublicJobRequestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PublicJobRequestNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function createCraftsman(Category $category, Location $location, bool $withActiveSubscription): User
    {
        $craftsman = User::factory()->create([
            'role' => 'specialist',
            'is_active' => true,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'slug' => 'craftsman-' . uniqid(),
        ]);

        if ($withActiveSubscription) {
            $plan = Plan::create([
                'name' => 'Pro',
                'slug' => 'pro-' . uniqid(),
                'price_monthly' => 99,
            ]);

            Subscription::create([
                'user_id' => $craftsman->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'started_at' => now(),
                'ends_at' => null,
            ]);
        }

        return $craftsman;
    }

    public function test_only_subscribed_matching_craftsmen_are_notified(): void
    {
        Notification::fake();

        $category = Category::factory()->create();
        $otherCategory = Category::factory()->create();
        $location = Location::factory()->create();

        $subscribedMatching = $this->createCraftsman($category, $location, withActiveSubscription: true);
        $unsubscribedMatching = $this->createCraftsman($category, $location, withActiveSubscription: false);
        $subscribedWrongCategory = $this->createCraftsman($otherCategory, $location, withActiveSubscription: true);

        $response = $this->post(route('public-request.store'), [
            'name' => 'Maria Vasile',
            'phone' => '0740000000',
            'email' => 'maria@example.test',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'title' => 'Montaj centrala',
            'description' => str_repeat('a', 25),
            'urgency' => 'flexible',
        ]);

        $response->assertRedirect();

        $jobRequest = PublicJobRequest::firstOrFail();
        $this->assertSame(1, $jobRequest->notified_craftsmen);

        Notification::assertSentTo($subscribedMatching, NewPublicJobRequestNotification::class);
        Notification::assertNotSentTo($unsubscribedMatching, NewPublicJobRequestNotification::class);
        Notification::assertNotSentTo($subscribedWrongCategory, NewPublicJobRequestNotification::class);
    }
}
