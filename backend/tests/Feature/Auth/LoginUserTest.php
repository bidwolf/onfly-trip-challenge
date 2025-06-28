<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Str;
use Tests\TestCase;

class LoginUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * Testing if a created user can make a login
     */
    public function test_recently_user_can_make_login(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => 'P4assword!',
            'password_confirmation' => 'P4assword!'
        ];
        $createdUserResponse = $this->post(route('register'), $data);
        $createdUserResponse->assertStatus(201);
        $loginResponse = $this->postJson(route('login'), Arr::only($data, ['email', 'password']));
        $loginResponse->assertStatus(200);
        $loginResponse->assertJson(function (AssertableJson $json) {
            $json->has('token');
            $json->has('type');
            $json->has('expires_in');
            $json->whereAllType([
                'token' => 'string',
                'type' => 'string',
                'expires_in' => 'integer',
            ]);
        });
    }
    /**
     * Test non existent user receive unauthorized status code
     */
    public function test_non_existent_user_receive_unauthorized(): void
    {
        $password = Str::random(9);
        $email = $this->faker->safeEmail;
        $this->assertDatabaseCount('users', 0);
        $loginResponse = $this->postJson(route('login'), ['email' => $email, 'password' => $password]);
        $loginResponse->assertUnauthorized();
    }
    /**
     * Test existent user with wrong password receive unauthorized status code
     */
    public function test_existent_user_receive_unauthorized()
    {
        $password = Str::random(9);
        $wrongPassword = 'wrong';
        $user = User::factory()->create(['password' => $password]);
        $successLoginResponse = $this->postJson(route('login'), ['email' => $user->email, 'password' => $password]);
        $successLoginResponse->assertSuccessful();
        $unauthorizedloginResponse = $this->postJson(route('login'), ['email' => $user->email, 'password' => $wrongPassword]);
        $unauthorizedloginResponse->assertUnauthorized();
    }
    /**
     * Test logged user can use /api/user endpoint
     */
    public function test_logged_user_can_use_protected_endpoint()
    {
        $password = Str::random(9);
        $user = User::factory()->create(['password' => $password]);
        $loginResponse = $this->postJson(route('login'), ['email' => $user->email, 'password' => $password]);
        $loginResponse->assertSuccessful();
        $token = $loginResponse['token'];
        $protectedRouteResponse = $this->getJson(uri: route('me'), headers: ['Authorization' => "Bearer $token"]);
        $protectedRouteResponse->assertSuccessful();
    }
}
