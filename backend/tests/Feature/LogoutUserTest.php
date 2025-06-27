<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Testing user logged can do logout
     */
    public function test_logged_user_can_logout(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/api/auth/logout');
        $response->assertStatus(200);
    }
    /**
     * Testing user recently registered can do logout
     */
    public function test_recently_registered_user_can_logout(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => 'P4assword!',
            'password_confirmation' => 'P4assword!'
        ];
        $userResponse = $this->post(route('register'), $data);
        $token = $userResponse['token'];
        $authenticatedResponse = $this->getJson(uri: '/api/user', headers: ['Authorization' => "bearer $token"]);
        $authenticatedResponse->assertSuccessful();
        $logoutResponse = $this->postJson(uri: route('logout'), headers: ['Authorization' => "bearer $token"]);
        $logoutResponse->assertSuccessful();
        $this->assertEquals('Usuário deslogado com sucesso.', $logoutResponse['message']);
    }
    /**
     * Testing unauthenticated user trying to logout
     */
    public function test_unauthenticated_logout(): void
    {
        $logoutResponse = $this->postJson(uri: route('logout'));
        $logoutResponse->assertStatus(401);
    }
}
