<?php

namespace Tests\Feature\TravelOrder;

use App\Enum\TravelOrderStatus;
use App\Models\TravelOrder;
use App\Models\User;
use App\Traits\AssertAllResourcesExistsWithKeyValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;


class ListTravelOrdersTest extends TestCase
{
    use WithFaker, RefreshDatabase, AssertAllResourcesExistsWithKeyValue;
    protected User $user;
    protected string $existingOrderId;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $orders = TravelOrder::factory()
            ->count(3)
            ->for($this->user)
            ->create();
        $this->existingOrderId = $orders[0]->id;
    }
    /**
     * A travel order should be returned by its own user creator.
     */
    public function test_travel_order_should_be_returned_to_owner(): void
    {
        assertNotNull($this->existingOrderId);
        assertEquals(TravelOrder::find($this->existingOrderId)->id, $this->existingOrderId);
        $response = $this->actingAs($this->user)->get(route('travel-orders.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    "requester_name",
                    "status",
                    "destination",
                    "departure_date",
                    "return_date",
                ]
            ]
        ]);
    }
    /**
     * A common user cannot see travel orders from other users.
     */
    public function test_common_user_cannot_see_travel_orders_from_others(): void
    {
        assertNotNull($this->existingOrderId);
        assertEquals(TravelOrder::find($this->existingOrderId)->id, $this->existingOrderId);
        $other_user = User::factory()
            ->has(TravelOrder::factory()->count(3))
            ->create();
        $response_other = $this->actingAs($other_user)->get(route('travel-orders.index'));
        $response_current = $this->actingAs($this->user)->get(route('travel-orders.index'));
        $response_other->assertSuccessful();
        $response_current->assertSuccessful();
        $this->assertNotEqualsCanonicalizing($response_other['data'], $response_current['data']);
        $this->assertAllResourcesExistsWithKeyValue(
            key: 'user_id',
            value: $other_user->id,
            resources: $response_other['data']
        );
        $this->assertAllResourcesExistsWithKeyValue(
            key: 'user_id',
            value: $this->user->id,
            resources: $response_current['data']
        );
        $this->assertResourcesNotHaveKeyWithValue(
            key: 'user_id',
            value: $this->user->id,
            resources: $response_other['data']
        );
        $this->assertResourcesNotHaveKeyWithValue(
            key: 'user_id',
            value: $other_user->id,
            resources: $response_current['data']
        );
    }
    /**
     * A administrator should be able to see all orders on the system.
     */
    public function test_travel_orders_should_be_returned_to_other_admin_user(): void
    {
        assertNotNull($this->existingOrderId);
        assertEquals(TravelOrder::find($this->existingOrderId)->id, $this->existingOrderId);
        $other_user = User::factory()
            ->isAdmin()
            ->has(
                TravelOrder::factory()->count(3)
            )
            ->create();
        $response_other = $this->actingAs($other_user)->get(route('travel-orders.index'));
        $response_current = $this->actingAs($this->user)->get(route('travel-orders.index'));
        $response_other->assertSuccessful();
        $response_current->assertSuccessful();
        $this->assertNotEqualsCanonicalizing($response_other['data'], $response_current['data']);

        $this->assertResourcesNotHaveKeyWithValue(
            key: 'user_id',
            value: $other_user->id,
            resources: $response_current['data']
        );

        $this->assertResourceHaveKeyWithValue(
            key: 'user_id',
            value: $other_user->id,
            resources: $response_other['data']
        );
        $this->assertResourceHaveKeyWithValue(
            key: 'user_id',
            value: $this->user->id,
            resources: $response_other['data']
        );
        $this->assertAllResourcesExistsWithKeyValue(
            key: 'user_id',
            value: $this->user->id,
            resources: $response_current['data']
        );
    }
    /**
     * A unauthenticated user cannot see any travelOrder
     */
    public function test_travel_orders_should_not_be_returned_to_unauthenticated_users(): void
    {
        assertNotNull($this->existingOrderId);
        assertEquals(TravelOrder::find($this->existingOrderId)->id, $this->existingOrderId);
        $response = $this->getJson(route('travel-orders.index'));
        $response->assertUnauthorized();
        $response->assertJsonStructure([
            'message'
        ]);
    }
    public function test_filter_travel_orders_by_status(): void
    {
        // create orders with different statuses
        $pendingOrder  = TravelOrder::factory()->for($this->user)->create(['status' => TravelOrderStatus::Pending->value]);
        $approvedOrder = TravelOrder::factory()->for($this->user)->create(['status' => TravelOrderStatus::Approved->value]);

        $response = $this->actingAs($this->user)
            ->getJson(route('travel-orders.index', ['status' => TravelOrderStatus::Pending->value]));

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'status', 'destination', 'departure_date', 'return_date']]]);

        // only pending orders are returned
        $this->assertAllResourcesExistsWithKeyValue(
            key: 'status',
            value: TravelOrderStatus::Pending->value,
            resources: $response['data']
        );
        $this->assertResourcesNotHaveKeyWithValue(
            key: 'status',
            value: TravelOrderStatus::Approved->value,
            resources: $response['data']
        );
    }

    public function test_filter_travel_orders_by_destination(): void
    {
        $nyOrder = TravelOrder::factory()->for($this->user)->create(['destination' => 'New York']);
        $laOrder = TravelOrder::factory()->for($this->user)->create(['destination' => 'Los Angeles']);

        $response = $this->actingAs($this->user)
            ->getJson(route('travel-orders.index', ['destination' => 'New York']));

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'status', 'destination', 'departure_date', 'return_date']]]);

        $this->assertAllResourcesExistsWithKeyValue(
            key: 'destination',
            value: 'New York',
            resources: $response['data']
        );
        $this->assertResourcesNotHaveKeyWithValue(
            key: 'destination',
            value: 'Los Angeles',
            resources: $response['data']
        );
    }

    public function test_filter_travel_orders_by_start_and_end_date(): void
    {
        $newUser = User::factory()
            ->create();
        $orderBefore  = TravelOrder::factory()->for($newUser)->create(['departure_date' => '2021-01-01', 'return_date' => '2022-01-01']);
        $orderInRange = TravelOrder::factory()->for($newUser)->create(['departure_date' => '2022-01-01', 'return_date' => '2023-01-01']);
        $orderAfter   = TravelOrder::factory()->for($newUser)->create(['departure_date' => '2023-01-01', 'return_date' => '2024-01-01']);
        $response = $this->actingAs($newUser)
            ->getJson(uri: route('travel-orders.index', [
                'start_date' => $orderInRange->departure_date,
                'end_date'   => $orderInRange->return_date,
            ]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'status',
                        'destination',
                        'departure_date',
                        'return_date'
                    ]
                ]
            ]);
        $this->assertResourceHaveKeyWithValue(
            key: 'departure_date',
            value: $orderInRange->departure_date,
            resources: $response['data']
        );
        $this->assertResourceHaveKeyWithValue(
            key: 'return_date',
            value: $orderInRange->return_date,
            resources: $response['data']
        );
        $this->assertResourcesNotHaveKeyWithValue(
            key: 'departure_date',
            value: $orderBefore->departure_date,
            resources: $response['data']
        );
        $this->assertResourcesNotHaveKeyWithValue(
            key: 'departure_date',
            value: $orderAfter->departure_date,
            resources: $response['data']
        );
    }
}
