<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition()
    {
        return [
            'content' => $this->faker->sentence,
            'location' => $this->faker->randomElement(['header', 'footer', 'sidebar']),
        ];
    }
}
