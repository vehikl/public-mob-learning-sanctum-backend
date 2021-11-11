<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        User::factory()->times(20)->create();
    }

    public function test_a_guest_cannot_get_the_list_of_users()
    {
        $this->getJson('/users')->assertUnauthorized();
    }

    public function test_a_user_can_get_the_list_of_users()
    {
        $theAuthenticatedUser = User::query()->first();

        $this->actingAs($theAuthenticatedUser)
            ->getJson('/users')
            ->assertSuccessful()
            ->assertJson(User::all()->toArray());
    }
}
