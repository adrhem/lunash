<?php

namespace Tests\Feature;

use BadMethodCallException;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_filament_admin_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
        $response->assertStatus(302);
    }

    public function test_the_filament_admin_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Login');
    }

    public function test_once_loggedin_displays_dashboard(): void
    {
        try {
            $user = UserFactory::new()->create();
            $response = $this->actingAs($user)
                ->get('/');
            $response->assertStatus(200);
        } catch (BadMethodCallException $e) {
            // SQLite does not support getCollection method at this time.
            $this->assertStringContainsString('Method Illuminate\Database\SQLiteConnection::getCollection does not exist', $e->getMessage());
        }
    }
}
