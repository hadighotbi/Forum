<?php

namespace Tests\Feature;

use App\Models\Reply;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use DatabaseMigrations;

    function test_a_guest_cannot_favorite_anything()
    {
        $this->withExceptionHandling()
            ->post('replies/1/favorites')
            ->assertRedirect('login');
    }


    function test_an_authorized_user_can_favorite_any_reply()
    {
        $this->signIn();
        $reply = Reply::factory()->create();   //also created a thread in background

        $this->post('replies/' . $reply->id . '/favorites');
        $this->assertEquals(1, $reply->favoritesCount);
    }

    function test_an_authorized_user_can_unfavorite_a_reply()
    {
        $this->signIn();
        $reply = Reply::factory()->create();

        $reply->favorite();

        $this->delete('replies/' . $reply->id . '/favorites');
        $this->assertEquals(0, $reply->favoritesCount); //fresh instance
    }

    function test_an_authenticated_user_may_only_favorite_a_reply_once()
    {
        $this->signIn();
        $reply = Reply::factory()->create();   //also created a thread in background

        try {
            $this->post('replies/' . $reply->id . '/favorites');
            $this->post('replies/' . $reply->id . '/favorites');
        } catch (\Exception $e) {
            $this->fail('Can not favorite twice');
        }

        $this->assertEquals(1, $reply->favoritesCount);
    }
}
