<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderItemControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_retrieve_all_order_items()
    {
        // Create an order item
        OrderItem::factory()->count(3)->create();

        // Call the index endpoint
        $response = $this->getJson('/api/order_items');

        // Assert the correct status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'order_id', 'product_id', 'order_qty', 'total_amount', 'order_date'],
                ],
                'count',
            ]);

        // Assert that the count matches the number of order items created
        $this->assertEquals(3, $response['count']);
    }

    /** @test */
    public function it_can_create_a_new_order_item_with_valid_data()
    {
        // Create a user, order, and product
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Create related models
        $category = Category::factory()->create();
        $subcategory = SubCategory::factory()->create(['category_id' => $category->id]);
        $product = Product::factory()->create(['category_id' => $category->id, 'subcategory_id' => $subcategory->id]);
        $order = Order::factory()->create(['user_id' => $user->id]);

        // Order item data
        $orderItemData = [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'order_qty' => 2,
            'total_amount' => '50.00',
            'order_date' => now()->format('Y-m-d'),
        ];

        // Call the store endpoint
        $response = $this->postJson('/api/order_items', $orderItemData);

        // Assert the order item is created successfully
        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'order_id',
                'product_id',
                'order_qty',
                'total_amount',
                'order_date',
                'created_at',
                'updated_at',
            ]);

        // Ensure the order item is in the database
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'order_qty' => 2,
        ]);
    }

    /** @test */
    public function it_cannot_create_an_order_item_with_invalid_data()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Invalid order item data (missing required fields)
        $orderItemData = [
            'order_id' => '', // Missing
            'product_id' => '', // Missing
            'order_qty' => '',
            'total_amount' => '',
            'order_date' => '',
        ];

        // Call the store endpoint
        $response = $this->postJson('/api/order_items', $orderItemData);

        // Assert validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_id', 'product_id', 'order_qty', 'total_amount', 'order_date']);
    }

    /** @test */
    public function it_can_show_a_specific_order_item()
    {
        // Create a user, order, product, and order item
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $orderItem = OrderItem::factory()->create();

        // Call the show endpoint
        $response = $this->getJson("/api/order_items/{$orderItem->id}");

        // Assert that the order item is retrieved successfully
        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'order_id',
                'product_id',
                'order_qty',
                'total_amount',
                'order_date',
                'created_at',
                'updated_at',
            ]);
    }

    /** @test */
    public function it_returns_404_if_order_item_not_found()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Attempt to retrieve a non-existent order item
        $response = $this->getJson('/api/order_items/9999'); // Non-existent ID

        // Assert the 404 response
        $response->assertStatus(404)
            ->assertJson(['message' => 'OrderItem not found']);
    }

    /** @test */
    public function it_can_update_an_order_item_with_valid_data()
    {
        // Create a user, order, product, and order item
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $orderItem = OrderItem::factory()->create();

        // Update data
        $updateData = [
            'order_qty' => 5,
            'total_amount' => '100.00',
            'order_date' => now()->format('Y-m-d'),
        ];

        // Call the update endpoint
        $response = $this->putJson("/api/order_items/{$orderItem->id}", $updateData);

        // Assert the order item was updated successfully
        $response->assertStatus(200)
            ->assertJson([
                'id' => $orderItem->id,
                'order_qty' => 5,
                'total_amount' => '100.00',
            ]);

        // Ensure the order item was updated in the database
        $this->assertDatabaseHas('order_items', [
            'id' => $orderItem->id,
            'order_qty' => 5,
            'total_amount' => '100.00',
        ]);
    }

    /** @test */
    public function it_cannot_update_an_order_item_with_invalid_data()
    {
        // Create a user, order, product, and order item
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $orderItem = OrderItem::factory()->create();

        // Invalid update data
        $invalidData = [
            'order_qty' => '',
            'total_amount' => '',
            'order_date' => '',
        ];

        // Call the update endpoint
        $response = $this->putJson("/api/order_items/{$orderItem->id}", $invalidData);

        // Assert validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_qty', 'total_amount', 'order_date']);
    }

    /** @test */
    public function it_can_delete_an_order_item_by_the_owner()
    {
        // Create a user, order, product, and order item
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $orderItem = OrderItem::factory()->create();

        // Call the delete endpoint
        $response = $this->deleteJson("/api/order_items/{$orderItem->id}");

        // Assert the order item was deleted successfully
        $response->assertStatus(204);

        // Ensure the order item is no longer in the database
        $this->assertDatabaseMissing('order_items', ['id' => $orderItem->id]);
    }

    /** @test */
    public function it_returns_404_when_deleting_a_non_existent_order_item()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Attempt to delete a non-existent order item
        $response = $this->deleteJson('/api/order_items/9999'); // Non-existent ID

        // Assert the 404 response
        $response->assertStatus(404)
            ->assertJson(['message' => 'Order not found']);
    }

    /** @test */
    public function it_cannot_delete_an_order_item_that_does_not_belong_to_the_user()
    {
        // Create two users
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        // Create an order item belonging to another user
        $orderItem = OrderItem::factory()->create(['user_id' => $anotherUser->id]);

        // Authenticate as the first user
        $this->actingAs($user, 'api');

        // Attempt to delete the order item
        $response = $this->deleteJson("/api/order_items/{$orderItem->id}");

        // Assert a 404 response since the order item does not belong to the user
        $response->assertStatus(404)
            ->assertJson(['message' => 'Order not found']);

        // Ensure the order item still exists in the database
        $this->assertDatabaseHas('order_items', ['id' => $orderItem->id]);
    }
}
