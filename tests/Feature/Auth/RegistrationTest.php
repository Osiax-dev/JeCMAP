<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * L'inscription publique est fermée (phase 1 sécurité) : ces tests remplacent ceux
 * de Breeze qui vérifiaient qu'un visiteur pouvait créer un compte.
 */
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_is_not_available(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_visitor_cannot_create_an_account(): void
    {
        $response = $this->post('/register', [
            'name' => 'Pirate',
            'email' => 'pirate@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'is_admin' => true,
        ]);

        $response->assertNotFound();
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'pirate@example.com']);
    }
}
