<?php

use App\Enum\TravelOrderStatus;
use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApproveTravelOrderTest extends TestCase
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
   * A guest user cannot be authorized to approve an order
   */
  public function test_guest_got_unauthorized_while_try_to_approve_order(): void
  {
    $response = $this->put(uri: route('travel-orders.approve', ['travel_order' => 1]));

    $response->assertUnauthorized();
  }
  /**
   * A common user cannot approve orders
   */
  public function test_common_user_cannot_approve_orders(): void
  {
    $order_by_another_user = TravelOrder::factory(['status' => TravelOrderStatus::Pending])
      ->for(User::factory())
      ->create();
    $order_by_own = TravelOrder::factory(['status' => TravelOrderStatus::Pending])
      ->for($this->common_user)
      ->create();
    $approve_others_orders_response = $this->actingAs($this->common_user)
      ->put(uri: route('travel-orders.approve', $order_by_another_user));
    $approve_others_orders_response->assertForbidden();
    $approve_own_order_response = $this->actingAs($this->common_user)
      ->put(uri: route('travel-orders.approve', $order_by_own));
    $approve_own_order_response->assertForbidden();
  }
  /**
   * A admin user can approve any pending order
   */
  public function test_admin_user_can_approve_any_pending_order(): void
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
    $this->assertEquals($common_user_order->status, TravelOrderStatus::Pending);
    $this->assertEquals($order_by_own->status, TravelOrderStatus::Pending);
    $this->assertTrue($this->user_admin->is_admin);
    $approve_own_order_response = $this->actingAs($this->user_admin)
      ->put(uri: route('travel-orders.approve', $order_by_own));
    $approve_own_order_response->assertSuccessful();
    $approve_common_user_orders_response = $this->actingAs($this->user_admin)
      ->put(uri: route('travel-orders.approve', $common_user_order));
    $approve_common_user_orders_response->assertSuccessful();
    $approve_others_admin_orders_response = $this->actingAs($this->user_admin)
      ->put(uri: route('travel-orders.approve', $another_admin_order));
    $approve_others_admin_orders_response->assertSuccessful();
  }
  /**
   * A Admin cannot approve cancelled orders
   */
  public function test_admin_cannot_approve_cancelled_orders(): void
  {
    $approved_order = TravelOrder::factory(['status' => TravelOrderStatus::Cancelled->value])
      ->for(User::factory())
      ->create();
    $response = $this
      ->actingAs($this->user_admin)
      ->putJson(uri: route('travel-orders.approve', $approved_order));
    $response->assertForbidden();
  }
}
