<?php

namespace Tests\Feature\Auth;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A user should be able to make a registration
     */
    public function test_registration_user_success(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => 'P4assword!',
            'password_confirmation' => 'P4assword!'
        ];
        $response = $this->post('/api/auth/register', $data);
        $response->assertJson(function (AssertableJson $json) {
            $json->has('token');
            $json->has('type');
            $json->has('expires_in');
            $json->has('message');
            $json->whereAllType([
                'token' => 'string',
                'type' => 'string',
                'expires_in' => 'integer',
                'message' => 'string'
            ]);
        });
        $response->assertStatus(201);
    }
    public function test_regitration_user_should_fail_for_same_email(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => 'P4assword!',
            'password_confirmation' => 'P4assword!'
        ];
        $RegistrationResponse = $this->post('/api/auth/register', $data);
        $RegistrationResponse->assertCreated(); # Creates an user
        $duplicatedRegisterResponse = $this->post('/api/auth/register', $data); # Try to create a new user with the same email registered before
        $duplicatedRegisterResponse->assertUnprocessable()->assertJsonValidationErrors('email');
    }
}
