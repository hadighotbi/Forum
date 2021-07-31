<?php

namespace Database\Factories;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReplyFactory extends Factory
{
    protected $model = Reply::class;

    public function definition()
    {
        return [
            'thread_id' => function () {
                return Thread::factory()->create()->id;},
            'user_id' => function () {
                return User::factory()->create()->id;},
            'body' => $this->faker->paragraph()
        ];
    }
}
