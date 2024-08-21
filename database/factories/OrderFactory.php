<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'subtotal' => $this->faker->randomFloat(2, 20, 500),
            'shipping_cost' => $this->faker->randomFloat(2, 5, 50),
            'total' => $this->faker->randomFloat(2, 25, 550),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['pending', 'completed', 'canceled']),
        ];
    }
}
