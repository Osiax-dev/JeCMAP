<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    /*
     * Les deux tests Breeze de suppression de compte sont remplacés : la suppression par
     * l'utilisateur n'est volontairement pas proposée (risque de site sans administrateur,
     * échec de clé étrangère created_by). Les comptes se gèrent avec `php artisan jecmap:admin`.
     */
    public function test_admin_cannot_delete_their_own_account_from_the_site(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->delete('/profile', ['password' => 'password'])
            ->assertMethodNotAllowed();

        $this->assertAuthenticated();
        $this->assertNotNull($user->fresh());
    }

    public function test_ordinary_user_cannot_access_or_update_profile(): void
    {
        $user = User::factory()->create(['name' => 'Nom initial']);

        $this->actingAs($user)->get('/profile')->assertForbidden();
        $this->actingAs($user)->patch('/profile', ['name' => 'Piraté', 'email' => $user->email])->assertForbidden();

        $this->assertSame('Nom initial', $user->fresh()->name);
    }

    public function test_guest_is_redirected_from_profile(): void
    {
        $this->get('/profile')->assertRedirect('/login');
    }

    public function test_profile_update_rejects_an_email_already_used(): void
    {
        User::factory()->create(['email' => 'pris@example.com']);
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->from('/profile')
            ->patch('/profile', ['name' => 'Admin', 'email' => 'pris@example.com'])
            ->assertSessionHasErrors('email')
            ->assertRedirect('/profile');
    }

    public function test_admin_can_change_password_from_profile_page(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->get('/profile')
            ->assertOk()
            ->assertSee('Changer le mot de passe')
            ->assertSee(route('password.update'), false);

        $this->actingAs($user)->from('/profile')->put('/password', [
            'current_password' => 'password',
            'password' => 'nouveau-mot-de-passe',
            'password_confirmation' => 'nouveau-mot-de-passe',
        ])->assertSessionHasNoErrors()->assertRedirect('/profile');

        $this->assertTrue(Hash::check('nouveau-mot-de-passe', $user->fresh()->password));
    }
}
