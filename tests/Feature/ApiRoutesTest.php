<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        User::factory()->times(20)->create();
    }


    public function test_a_user_can_get_an_api_token_when_they_provide_proper_credentials()
    {
        /** @var $user User */
        $user = User::query()->first();
        $password = 'password';
        $user->password = Hash::make($password);
        $user->save();

        $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => $password
        ])->assertSuccessful()->assertJsonStructure(['access_token']);

        $this->assertAuthenticatedAs($user);
        $this->assertCount(1, $user->tokens);
    }

    public function test_a_user_can_get_an_api_token_when_they_provide_gibberish_credentials()
    {
        /** @var $user User */
        $user = User::query()->first();
        $password = 'password';
        $user->password = Hash::make($password);
        $user->save();

        $this->postJson(route('login'), [
            'email' => 'skdjksjdb@gmail.com',
            'password' => 'foobar'
        ])->assertUnauthorized();

        $this->assertCount(0, $user->tokens);
    }

    public function test_a_user_can_get_an_api_token_and_then_see_all_users()
    {
        // make 19 other users
        User::factory()->times(19)->create();

        /** @var $user User */
        $user = User::query()->first();
        $password = 'password';
        $user->password = Hash::make($password);
        $user->save();

        $tokenResponse = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => $password
        ])->assertSuccessful();
        Auth::logout();

        $token = $tokenResponse->json('access_token');
        $this->getJson(route('all-users'), ['Authorization' => "Bearer $token"])
            ->assertSuccessful()
            ->assertJson(User::all()->toArray());
//            ->assertJson([
//
//            ]);
    }


}
