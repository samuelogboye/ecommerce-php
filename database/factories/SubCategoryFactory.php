<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubCategoryFactory extends Factory
{
    protected $model = SubCategory::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
        ];
    }
}
