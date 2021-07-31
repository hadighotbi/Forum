<?php

namespace Database\Factories;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChannelFactory extends Factory
{
    protected $model = Channel::class;

    public function definition()
    {
         $name = $this->faker->word();
        return [
            'name' => $name,
            'slug' => $name
        ];
    }
}
