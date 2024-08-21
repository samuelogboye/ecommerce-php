<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        $category = Category::inRandomOrder()->first();

        // Select a subcategory that belongs to the selected category
        $subcategory = SubCategory::where('category_id', $category->id)
                                  ->inRandomOrder()
                                  ->first();

        return [
            'name' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'qty' => $this->faker->numberBetween(1, 100),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'category_id' => $category->id,
            'subcategory_id' => $subcategory ? $subcategory->id : null,
            'featured_image' => $this->faker->imageUrl(640, 480, 'products', true),
            'rank' => $this->faker->numberBetween(0, 5),
            'status' => $this->faker->randomElement(['available', 'out of stock']),
        ];
    }
}
