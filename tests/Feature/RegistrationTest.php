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

        $this->post(route('register'),[
            'name' => 'john',
            'email' =>'john@example.com',
            'password' => 'foobar123',
            'password_confirmation' => 'foobar123'
        ]);

        Mail::assertQueued(PleaseConfirmYourEmail::class);
    }

    function test_users_can_fully_confirm_their_email_addresses() //pass : 8 char
    {
        Mail::fake();

        $this->post(route('register'),[
            'name' => 'john',
            'email' =>'john@example.com',
            'password' => 'foobar123',
            'password_confirmation' => 'foobar123'
      ]);

        $user = User::whereName('john')->first();

        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token);

        $this->get(route('register.confirm', ['token' => $user->confirmation_token]))
            ->assertRedirect(route('threads'));

        $this->assertTrue($user->fresh()->confirmed);
   }

   function test_confirming_an_invalid_token()
   {
       $this->get(route('register.confirm', ['token' => 'invalid']))
           ->assertRedirect(route('threads'))
           ->assertSessionHas('flash'); //Unknown Token
   }
}
