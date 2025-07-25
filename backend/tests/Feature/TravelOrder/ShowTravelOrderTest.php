<?php

namespace Tests\Feature\TravelOrder;

use App\Models\TravelOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;

class ShowTravelOrderTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    protected User $user;
    protected string $existingOrderId;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $order = TravelOrder::create([

            'user_id' => $this->user->id,
            'requester_name' => $this->faker->name,
            'destination' => $this->faker->city,
            'departure_date' => Carbon::tomorrow()->format('Y-m-d'),
            'return_date' => Carbon::tomorrow()->addMonth(1)->format('Y-m-d')

        ]);
        $this->existingOrderId = $order->id;
    }
    /**
     * A travel order should be returned by its own user creator.
     */
    public function test_travel_order_should_be_returned_to_owner(): void
    {
        assertNotNull($this->existingOrderId);
        assertEquals(TravelOrder::find($this->existingOrderId)->id, $this->existingOrderId);
        $response = $this->actingAs($this->user)->get(route('travel-orders.show', ['travel_order' => $this->existingOrderId]));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                "requester_name",
                "status",
                "destination",
                "departure_date",
                "return_date",
                "user_id",
                "created_at",
            ]
        ]);
    }
    /**
     * A common user cannot see a travel order from other users.
     */
    public function test_travel_order_should_not_be_returned_to_other_common_user(): void
    {
        assertNotNull($this->existingOrderId);
        assertEquals(TravelOrder::find($this->existingOrderId)->id, $this->existingOrderId);
        $other_user = User::factory()->create();
        $response = $this->actingAs($other_user)->get(route('travel-orders.show', ['travel_order' => $this->existingOrderId]));
        $response->assertNotFound();
        $response->assertJsonStructure([
            'message'
        ]);
    }
    /**
     * A travel order should be returned to other user with admin permission.
     */
    public function test_travel_order_should_be_returned_to_other_admin_user(): void
    {
        assertNotNull($this->existingOrderId);
        assertEquals(TravelOrder::find($this->existingOrderId)->id, $this->existingOrderId);
        $other_user = User::factory()->isAdmin()->create();
        $response = $this->actingAs($other_user)->get(route('travel-orders.show', ['travel_order' => $this->existingOrderId]));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                "requester_name",
                "status",
                "destination",
                "departure_date",
                "return_date",
                "user_id",
                "created_at",
                "user" => [
                    'name',
                    'email'
                ]
            ]
        ]);
    }
    /**
     * A unauthenticated user cannot see any travelOrder
     */
    public function test_travel_order_should_not_be_returned_to_unauthenticated_users(): void
    {
        assertNotNull($this->existingOrderId);
        assertEquals(TravelOrder::find($this->existingOrderId)->id, $this->existingOrderId);
        $response = $this->getJson(route('travel-orders.show', ['travel_order' => $this->existingOrderId]));
        $response->assertUnauthorized();
        $response->assertJsonStructure([
            'message'
        ]);
    }
}
