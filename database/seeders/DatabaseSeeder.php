<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    AddressInfo,
    Banner,
    Category,
    Order,
    OrderItem,
    Product,
    SubCategory,
    Tag,
    Transaction,
    User,
    View
};

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Categories first
        $categories = Category::factory(5)->create();

        // Create Subcategories for existing categories
        $categories->each(function ($category) {
            SubCategory::factory(3)->create(['category_id' => $category->id]);
        });

        // Create Users
        User::factory(10)->create()->each(function ($user) {
            // Create Address Info for each user
            AddressInfo::factory(1)->create(['user_id' => $user->id]);

            // Create Orders and related Order Items
            Order::factory(2)->create(['user_id' => $user->id])->each(function ($order) {
                OrderItem::factory(3)->create(['order_id' => $order->id]);
                Transaction::factory(1)->create(['order_id' => $order->id]);
            });
        });

        // Create Products
        Product::factory(20)->create()->each(function ($product) {
            // Attach tags to products
            $tags = Tag::factory(3)->create();
            $product->tags()->attach($tags);

            // Create views for products
            View::factory(5)->create(['product_id' => $product->id]);
        });

        // Create Banners
        Banner::factory(3)->create();
    }
}
