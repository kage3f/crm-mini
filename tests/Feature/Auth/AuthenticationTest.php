<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;
use App\Notifications\Auth\ResetPasswordQueuedNotification;
use App\Notifications\Auth\VerifyEmailQueuedNotification;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionsSeeder::class);
    }

    public function test_user_can_register_and_is_redirected_to_verification_notice(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'Maria Silva')
            ->set('company', 'Acme LTDA')
            ->set('email', 'maria@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('register')
            ->assertRedirect(route('verification.notice'));

        $this->assertAuthenticated();

        $user = User::where('email', 'maria@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);
        $this->assertSame('Acme LTDA', $user->company->name);
        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123'),
        ]);

        $this->withSession([]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'secret123')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123'),
        ]);

        $this->withSession([]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'wrong-pass')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_forgot_password_sends_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->withSession([]);

        Livewire::test(ForgotPassword::class)
            ->set('email', $user->email)
            ->call('sendResetLink')
            ->assertHasNoErrors();

        Notification::assertSentTo($user, ResetPasswordQueuedNotification::class);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', $user->email)
            ->set('password', 'new-secret123')
            ->set('password_confirmation', 'new-secret123')
            ->call('resetPassword')
            ->assertRedirect(route('login'));

        $user->refresh();
        $this->assertTrue(Hash::check('new-secret123', $user->password));
    }

    public function test_email_verification_resend_sends_notification_for_unverified_user(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $this->actingAs($user);
        $this->withSession([]);

        Livewire::test(VerifyEmail::class)
            ->call('resendEmail')
            ->assertHasNoErrors();

        Notification::assertSentTo($user, VerifyEmailQueuedNotification::class);
    }
}
