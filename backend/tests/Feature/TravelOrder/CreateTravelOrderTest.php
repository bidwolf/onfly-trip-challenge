<?php

namespace Tests\Feature\TravelOrder;

use App\Enum\TravelOrderStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

use Tests\TestCase;

class CreateTravelOrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    protected array $well_formated_order_dto;
    protected User $authenticatedUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->well_formated_order_dto =
            [
                'requester_name' => $this->faker->name,
                'destination' => $this->faker->city,
                'departure_date' => Carbon::tomorrow()->format('Y-m-d'),
                'return_date' => Carbon::tomorrow()->addMonth(1)->format('Y-m-d'),
                'price' => $this->faker->randomFloat(2, 0, 1000),
                'hosting' => $this->faker->domainName,
                'transportation' => $this->faker->domainName,
                'description' => $this->faker->domainName,
            ];
        $this->authenticatedUser = User::factory()->create();
    }
    /**
     * A request with authorized user and correct data should create order
     */
    public function test_correct_request_and_authorized_user_should_create_order()
    {
        $response = $this->actingAs($this->authenticatedUser)->postJson(uri: route('travel-orders.store'), data: $this->well_formated_order_dto);
        $response->assertSuccessful();
        $response->assertExactJsonStructure([
            'data' => [
                'id',
                'requester_name',
                'status',
                'destination',
                'departure_date',
                'return_date',
                'user_id',
                'created_at',
                'description',
                'hosting',
                'price',
                'transportation',
            ],
            'message'
        ]);
    }
    /**
     * A created order should always start with status Pending
     */
    public function test_created_order_starts_with_pending_status(): void
    {
        $response = $this->actingAs($this->authenticatedUser)->postJson(uri: route('travel-orders.store'), data: $this->well_formated_order_dto);
        $response->assertSuccessful();
        $this->assertEquals(TravelOrderStatus::Pending->value, $response['data']['status']);
    }
    /**
     * A request without a authorization token cannot be authorized
     */
    public function test_unauthorized_user_cannot_create_order(): void
    {
        $response = $this->postJson(uri: route('travel-orders.store'), data: []);
        $response->assertUnauthorized();
    }

    /**
     * A request with invalid requester_name should not be proceeded
     */
    public function test_order_with_invalid_requester_name_should_not_proceed(): void
    {
        $data = (array) $this->well_formated_order_dto;
        $data['requester_name'] = '';
        $response = $this->actingAs($this->authenticatedUser)->postJson(uri: route('travel-orders.store'), data: $data);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(
            [
                "requester_name",
            ],
        );

        $non_string_name_test = 1;
        $data['requester_name'] = $non_string_name_test;
        $response = $this->actingAs($this->authenticatedUser)->postJson(uri: route('travel-orders.store'), data: $data);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(
            [
                "requester_name",
            ],
        );

        $name_bigger_than_max_characters = Str::random(256);
        $data['requester_name'] = $name_bigger_than_max_characters;
        $response = $this->actingAs($this->authenticatedUser)->postJson(uri: route('travel-orders.store'), data: $data);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(
            [
                "requester_name",
            ],
        );
    }
    /**
     * A request with invalid destination should not be proceeded
     */
    public function test_order_with_invalid_destination_should_not_proceed(): void
    {
        $data = (array) $this->well_formated_order_dto;
        $data['destination'] = '';
        $response = $this->actingAs($this->authenticatedUser)->postJson(uri: route('travel-orders.store'), data: $data);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(
            [
                "destination",
            ],
        );

        $non_string_destination_test = 1;
        $data['destination'] = $non_string_destination_test;
        $response = $this->actingAs($this->authenticatedUser)->postJson(uri: route('travel-orders.store'), data: $data);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(
            [
                "destination",
            ],
        );

        $destination_bigger_than_max_characters = Str::random(256);
        $data['destination'] = $destination_bigger_than_max_characters;
        $response = $this->actingAs($this->authenticatedUser)->postJson(uri: route('travel-orders.store'), data: $data);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(
            [
                "destination",
            ],
        );
    }
    /**
     * A request with invalid departure_date should not proceeded
     */
    public function test_order_with_invalid_departure_date_should_not_proceed(): void
    {
        $user = User::factory()->create();
        $data = (array) $this->well_formated_order_dto;
        $data['departure_date'] = null;
        $response = $this->actingAs($user)->postJson(uri: route('travel-orders.store'), data: $data);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(
            [
                "departure_date",
            ]
        );

        $data['departure_date'] = 33;
        $response = $this->actingAs($user)->postJson(uri: route('travel-orders.store'), data: $data);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(
            [
                "departure_date",
            ]
        );

        $past_departure_date_test = Carbon::yesterday()->format('Y-m-d');
        $data['departure_date'] = $past_departure_date_test;
        $response = $this->actingAs($user)->postJson(uri: route('travel-orders.store'), data: $data);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(
            [
                "departure_date",
            ]
        );
    }
    /**
     * A request with invalid return_date should not proceeded
     */
    public function test_order_with_invalid_return_date_should_not_proceed(): void
    {
        $user = User::factory()->create();
        $data = (array) $this->well_formated_order_dto;
        $data['return_date'] = null;
        $response = $this->actingAs($user)->postJson(uri: route('travel-orders.store'), data: $data);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(
            [
                "return_date",
            ]
        );

        $data['return_date'] = 33;
        $response = $this->actingAs($user)->postJson(uri: route('travel-orders.store'), data: $data);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(
            [
                "return_date",
            ]
        );

        $before_departure_date_test = Carbon::createFromFormat('Y-m-d', $data['departure_date']);
        $data['return_date'] = $before_departure_date_test;
        $response = $this->actingAs($user)->postJson(uri: route('travel-orders.store'), data: $data);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(
            [
                "return_date",
            ]
        );
    }
}
