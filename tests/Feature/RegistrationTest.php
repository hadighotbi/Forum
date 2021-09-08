<?php

namespace Tests\Feature;

use App\Mail\PleaseConfirmYourEmail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

    function test_a_confirmation_email_sent_upon_registration()
    {
        Mail::fake();

        event(new Registered(User::factory()->create()));

        Mail::assertSent(PleaseConfirmYourEmail::class);
    }

    function test_users_can_fully_confirm_their_email_addresses() //pass : 8 char
    {
        $this->post('/register',[
            'name' => 'john',
            'email' =>'john@example.com',
            'password' => 'foobar123',
            'password_confirmation' => 'foobar123'
      ]);

        $user = User::whereName('john')->first();

        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token);

        $response = $this->get('/register/confirm?token='. $user->confirmation_token);

        $this->assertTrue($user->fresh()->confirmed);

        $response->assertRedirect('/threads');
   }
}
