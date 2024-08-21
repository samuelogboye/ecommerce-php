<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\View;
use Illuminate\Database\Eloquent\Factories\Factory;

class ViewFactory extends Factory
{
    protected $model = View::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'view_count' => $this->faker->numberBetween(0, 1000),
        ];
    }
}
