<?php

namespace Database\Seeders;

use App\Models\AddressInfo;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Tag;
use App\Models\Transaction;
use App\Models\User;
use App\Models\View;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Categories
        $categories = Category::factory(5)->create()->each(function ($category) {
            // Ensure the category is uniquely created
            Category::firstOrCreate(
                ['name' => $category->name],
                ['created_at' => now(), 'updated_at' => now()]
            );
        });

        // Create Subcategories for existing categories
        $categories->each(function ($category) {
            SubCategory::factory(3)->make(['category_id' => $category->id])
                ->each(function ($subCategory) use ($category) {
                    SubCategory::firstOrCreate(
                        ['name' => $subCategory->name],
                        ['category_id' => $category->id, 'created_at' => now(), 'updated_at' => now()]
                    );
                });
        });

        // Create Users
        User::factory(10)->create()->each(function ($user) {
            // Create Address Info for each user
            AddressInfo::factory(1)->create(['user_id' => $user->id]);

            // Create Orders for each user
            $orders = Order::factory(2)->create(['user_id' => $user->id]);

            // Create OrderItems for each Order
            $orders->each(function ($order) {
                OrderItem::factory(3)->create([
                    'order_id' => $order->id,
                ]);
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
