<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        return [
            // 'order_id' => Order::factory()->create()->id,
            'product_id' => Product::factory(), // ->create()->id,
            'order_qty' => $this->faker->numberBetween(1, 10),
            'order_date' => $this->faker->dateTimeThisYear,
            'total_amount' => $this->faker->randomFloat(2, 10, 200),
        ];
    }
}
