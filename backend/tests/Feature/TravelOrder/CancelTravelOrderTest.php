<?php

namespace Tests\Feature\TravelOrder;

use App\Enum\TravelOrderStatus;
use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class CancelTravelOrderTest extends TestCase
{
    use RefreshDatabase;
    protected User $common_user;
    protected User $user_admin;
    public function setUp(): void
    {
        parent::setUp();
        $this->common_user = User::factory()->create();
        $this->user_admin = User::factory()->isAdmin()->create();
        config(['broadcasting.default' => 'log']);
    }
    /**
     * A guest user cannot be authorized to cancel an order
     */
    public function test_guest_got_unauthorized_while_try_to_cancel_order(): void
    {
        $response = $this->put(route('travel-orders.cancel', ['travel_order' => 1]));

        $response->assertUnauthorized();
    }
    /**
     * A common user cannot cancel orders
     */
    public function test_common_user_cannot_cancel_orders(): void
    {
        $order_by_another_user = TravelOrder::factory()
            ->for(User::factory())
            ->create();
        $order_by_own = TravelOrder::factory()
            ->for($this->common_user)
            ->create();
        $cancel_others_orders_response = $this->actingAs($this->common_user)
            ->put(route('travel-orders.cancel', $order_by_another_user));
        $cancel_others_orders_response->assertForbidden();
        $cancel_own_order_response = $this->actingAs($this->common_user)
            ->put(route('travel-orders.cancel', $order_by_own));
        $cancel_own_order_response->assertForbidden();
    }
    /**
     * A admin user can cancel any pending order
     */
    public function test_admin_user_can_cancel_any_pending_order(): void
    {
        $common_user_order = TravelOrder::factory(['status' => TravelOrderStatus::Pending->value])
            ->for(User::factory())
            ->create();
        $another_admin_order = TravelOrder::factory(['status' => TravelOrderStatus::Pending->value])
            ->for(User::factory()->isAdmin())
            ->create();
        $order_by_own = TravelOrder::factory(['status' => TravelOrderStatus::Pending->value])
            ->for($this->user_admin)
            ->create();
        assertEquals($common_user_order->status, TravelOrderStatus::Pending);
        assertEquals($order_by_own->status, TravelOrderStatus::Pending);
        assertTrue($this->user_admin->is_admin);
        $cancel_own_order_response = $this->actingAs($this->user_admin)
            ->put(route('travel-orders.cancel', $order_by_own));
        $cancel_own_order_response->assertSuccessful();
        $cancel_common_user_orders_response = $this->actingAs($this->user_admin)
            ->put(route('travel-orders.cancel', $common_user_order));
        $cancel_common_user_orders_response->assertSuccessful();
        $cancel_others_admin_orders_response = $this->actingAs($this->user_admin)
            ->put(route('travel-orders.cancel', $another_admin_order));
        $cancel_others_admin_orders_response->assertSuccessful();
    }
    /**
     * A Admin cannot cancel an approved orders
     */
    public function test_admin_cannot_cancel_approved_orders(): void
    {
        $approved_order = TravelOrder::factory(['status' => TravelOrderStatus::Approved->value])
            ->for(User::factory())
            ->create();
        $response = $this
            ->actingAs($this->user_admin)
            ->putJson(uri: route('travel-orders.cancel', $approved_order));
        $response->assertForbidden();
    }
}
