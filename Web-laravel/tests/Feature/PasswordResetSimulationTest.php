<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordResetSimulationTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_can_be_rendered(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertSee('Lupa Password');
    }

    public function test_submitting_forgot_password_with_invalid_email_fails(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_submitting_forgot_password_with_valid_email_simulates_reset_link(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Link reset password disimulasikan!');
        $response->assertSessionHas('reset_link');

        $resetLink = session('reset_link');
        $this->assertNotNull($resetLink);
        $this->assertStringContainsString('/reset-password/', $resetLink);
        
        // Assert token was stored in database
        $this->assertTrue(DB::table('password_reset_tokens')->where('email', $user->email)->exists());
    }

    public function test_reset_password_page_can_be_rendered_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = 'some-secret-token';
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]));

        $response->assertStatus(200);
        $response->assertSee('Reset Password');
        $response->assertSee($user->email);
    }

    public function test_submitting_reset_password_with_invalid_token_fails(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make('correct-token'),
            'created_at' => now(),
        ]);

        $response = $this->from(route('password.reset', ['token' => 'invalid-token', 'email' => $user->email]))
            ->post(route('password.update'), [
                'token' => 'invalid-token',
                'email' => $user->email,
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertRedirect(route('password.reset', ['token' => 'invalid-token', 'email' => $user->email]));
        $response->assertSessionHasErrors(['email']);
    }

    public function test_submitting_reset_password_with_valid_token_updates_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $token = 'correct-token';
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', 'Password Anda berhasil direset! Silakan login.');

        // Assert database updated password
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));

        // Assert token deleted
        $this->assertFalse(DB::table('password_reset_tokens')->where('email', $user->email)->exists());
    }
}
