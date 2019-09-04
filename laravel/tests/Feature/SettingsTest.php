<?php

namespace Tests\Feature;

use App\Models\User ;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class SettingsTest extends TestCase
{
    /** @var \App\Models\User  */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function update_profile_info()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user)
            ->patchJson('/api/settings/profile', [
                'name' => 'Test User',
                'email' => 'test@test.app',
            ])
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'name', 'email']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Test User',
            'email' => 'test@test.app',
        ]);
    }

    /** @test */
    public function update_password()
    {
        $user = factory(User::class)->create(['password' => bcrypt('123')]);
        $this->actingAs($user)
            ->patchJson('/api/settings/password', [
                'password' => 'updated',
                'password_confirmation' => 'updated',
                'old_password' => '123',
            ])->dump()
            ->assertSuccessful();

        $this->assertTrue(Hash::check('updated', $user->password));
    }
}
