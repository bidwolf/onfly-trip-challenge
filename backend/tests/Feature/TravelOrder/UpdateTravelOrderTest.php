<?php

namespace Tests\Feature\TravelOrder;

use App\Enum\TravelOrderStatus;
use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTravelOrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A common user cannot update orders from others users
     */
    public function test_common_user_cannot_update_order_from_others(): void
    {
        $user = User::factory()->create();
        $order = TravelOrder::factory()->for(User::factory())->create();
        $response = $this->actingAs($user)->putJson(route('travel-orders.update', $order));

        $response->assertForbidden();
    }
    /**
     * A common user cannot update approved orders
     */
    public function test_common_user_cannot_update_approved_orders(): void
    {
        $user = User::factory()->create();
        $order = TravelOrder::factory([
            "status" => TravelOrderStatus::Approved
        ])->for($user)
            ->create();
        $response = $this->actingAs($user)->putJson(route('travel-orders.update', $order));
        $response->assertForbidden();
    }
    /**
     * A common user cannot update cancelled orders
     */
    public function test_common_user_cannot_update_cancelled_orders(): void
    {
        $user = User::factory()->create();
        $order = TravelOrder::factory([
            "status" => TravelOrderStatus::Cancelled
        ])->for($user)
            ->create();
        $response = $this->actingAs($user)->putJson(route('travel-orders.update', $order));
        $response->assertForbidden();
    }
    /**
     * A common user can update order their own orders
     */
    public function test_common_user_can_update_own_orders(): void
    {
        $user = User::factory()->create();
        $order = TravelOrder::factory(['status' => TravelOrderStatus::Pending])->for($user)->create();
        $response = $this->actingAs($user)->putJson(route('travel-orders.update', $order));
        $response->assertSuccessful();
    }
    /**
     * A administrator cannot update cancelled orders
     */
    public function test_administrators_cannot_update_cancelled_orders(): void
    {
        $user = User::factory()->isAdmin()->create();
        $order = TravelOrder::factory([
            "status" => TravelOrderStatus::Cancelled
        ])->for($user)
            ->create();
        $response = $this->actingAs($user)->putJson(route('travel-orders.update', $order));
        $response->assertForbidden();
    }
    /**
     * A administrator cannot update approved orders
     */
    public function test_administrators_cannot_update_approved_orders(): void
    {
        $user = User::factory()->isAdmin()->create();
        $order = TravelOrder::factory([
            "status" => TravelOrderStatus::Approved
        ])->for($user)
            ->create();
        $response = $this->actingAs($user)->putJson(route('travel-orders.update', $order));
        $response->assertForbidden();
    }
    /**
     * A administrator can update order from others
     */
    public function test_administrators_can_update_order_from_others(): void
    {
        $user = User::factory()->isAdmin()->create();
        $order = TravelOrder::factory()->for(User::factory())->create();
        $response = $this->actingAs($user)->putJson(route('travel-orders.update', $order));
        $response->assertSuccessful();
    }
}
