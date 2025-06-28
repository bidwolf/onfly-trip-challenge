<?php

namespace Tests\Feature\Auth;

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
        $response = $this->actingAs($user)->post(route('logout'));
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
        $authenticatedResponse = $this->getJson(uri: route('me'), headers: ['Authorization' => "bearer $token"]);
        $authenticatedResponse->assertSuccessful();
        $logoutResponse = $this->postJson(uri: route('logout'), headers: ['Authorization' => "bearer $token"]);
        $logoutResponse->assertSuccessful();
        $this->assertEquals('Usuário deslogado com sucesso.', $logoutResponse['message']);
    }
    /**
     * Testing user log in then logout
     */
    public function test_user_log_in_then_logout(): void
    {
        $passwordFake = 'fakepassword';
        $user = User::factory()->create(['password' => $passwordFake]);
        $loginResponse = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => $passwordFake
        ]);
        $loginResponse->assertSuccessful();
        $token = $loginResponse['token'];
        $authenticatedResponse = $this->getJson(uri: route('me'), headers: ['Authorization' => "bearer $token"]);
        $authenticatedResponse->assertSuccessful();
        $logoutResponse = $this->postJson(uri: route('logout'), headers: ['Authorization' => "bearer $token"]);
        $logoutResponse->assertSuccessful();
        $this->assertEquals('Usuário deslogado com sucesso.', $logoutResponse['message']);
    }
    /**
     * Testing user gets 401 in protected routes after logout
     */
    public function test_user_unauthorized_after_logout(): void
    {
        $passwordFake = 'fakepassword';
        $user = User::factory()->create(['password' => $passwordFake]);
        $loginResponse = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => $passwordFake
        ]);
        $loginResponse->assertSuccessful();
        $token = $loginResponse['token'];
        $authenticatedResponse = $this->getJson(uri: route('me'), headers: ['Authorization' => "bearer $token"]);
        $authenticatedResponse->assertSuccessful();
        $logoutResponse = $this->postJson(uri: route('logout'), headers: ['Authorization' => "bearer $token"]);
        $logoutResponse->assertSuccessful();
        $newrequest = $this->getJson(uri: route('me'), headers: ['Authorization' => "bearer $token"]);
        $newrequest->assertStatus(401);
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
