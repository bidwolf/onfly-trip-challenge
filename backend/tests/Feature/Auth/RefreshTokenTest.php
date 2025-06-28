<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RefreshTokenTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    protected User $user;
    protected $password;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('jwt.ttl', 1); // now the token will expire in one minute
        Config::set('jwt.refresh_ttl', 5); // now users cannot refresh token after 5 minutes
        $this->password = Str::random(9);
        $this->user = User::factory()->create(['password' => $this->password]);
    }

    /**
     * An unauthorized user cannot access the refresh route
     */
    public function test_unauthorized_access_to_refresh_route(): void
    {
        $response = $this->post(route('refresh'));
        $response->assertUnauthorized();
    }
    protected function get_user_token(): string
    {
        $loginResponse = $this->postJson(
            route('login'),
            [
                'email' => $this->user->email,
                'password' => $this->password
            ]
        );

        $loginResponse->assertSuccessful();
        $token = $loginResponse['token'];
        $this->assertNotNull($token);
        return $token;
    }
    /**
     * An authorized user can get refresh token
     */
    public function test_authorized_user_can_refresh_token(): void
    {

        $token = $this->get_user_token();
        $refreshResponse = $this->postJson(
            uri: route('refresh'),
            headers: ['Authorization' => "Bearer {$token}"]
        );
        $refreshResponse->assertSuccessful();
        $refreshResponse->assertJson(function (AssertableJson $json) {
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
     * An token with ttl in past but refresh_ttl in future can be refreshed
     */
    public function test_token_can_refresh_after_ttl_before_refresh_ttl(): void
    {
        $token = $this->get_user_token();
        Carbon::setTestNow(now()->addMinutes(Config::get('jwt.ttl') + 1)); // 1 minute after token expires
        $refreshResponse = $this->postJson(
            uri: route('refresh'),
            headers: ['Authorization' => "Bearer {$token}"]
        );
        $refreshResponse->assertSuccessful();
        Carbon::setTestNow(null); // reset time
    }
    /**
     * An token that refresh_ttl is already in the past cannot be refreshed
     */
    public function test_token_cannot_refresh_after_refresh_ttl(): void
    {
        $token = $this->get_user_token();
        Carbon::setTestNow(now()->addMinutes(Config::get('jwt.refresh_ttl') + 1)); // 1 minute after refresh token expiration
        $refreshResponse = $this->postJson(
            uri: route('refresh'),
            headers: ['Authorization' => "Bearer {$token}"]
        );
        $refreshResponse->assertUnauthorized();
        Carbon::setTestNow(null); // reset time
    }
}
