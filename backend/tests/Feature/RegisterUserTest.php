<?php

namespace Tests\Feature;

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
            $json->whereAllType([
                'token' => 'string',
                'type' => 'string',
                'expires_in' => 'integer'
            ]);
        });
        $response->assertStatus(201);
    }
}
