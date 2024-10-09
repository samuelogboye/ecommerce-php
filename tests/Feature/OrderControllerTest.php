<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_retrieve_orders_for_the_authenticated_user()
    {
        // Create a user and orders for that user
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Create multiple orders for the authenticated user
        Order::factory()->count(3)->create(['user_id' => $user->id]);

        // Call the index endpoint
        $response = $this->getJson('/api/orders');

        // Assert status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'subtotal', 'shipping_cost', 'total', 'status', 'order_items', 'transactions'],
                ],
                'count',
            ]);

        // Ensure the count matches the number of orders created
        $this->assertEquals(3, $response['count']);
    }

    /** @test */
    public function it_can_create_a_new_order_with_valid_data()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Data to create a new order
        $orderData = [
            'subtotal' => '100.00',
            'shipping_cost' => '10.00',
            'total' => '110.00',
            'status' => 'Pending',
        ];

        // Call the store endpoint
        $response = $this->postJson('/api/orders', $orderData);

        // Assert the order is created successfully
        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'subtotal',
                'shipping_cost',
                'total',
                'status',
                'created_at',
                'updated_at',
            ]);

        // Ensure the order is in the database
        $this->assertDatabaseHas('orders', ['subtotal' => '100.00', 'user_id' => $user->id]);
    }

    /** @test */
    public function it_cannot_create_an_order_with_invalid_data()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Invalid data (missing fields)
        $orderData = [
            'subtotal' => '',
            'shipping_cost' => '',
            'total' => '',
            'status' => '',
        ];

        // Call the store endpoint with invalid data
        $response = $this->postJson('/api/orders', $orderData);

        // Assert validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subtotal', 'shipping_cost', 'total', 'status']);
    }

    /** @test */
    public function it_can_show_a_specific_order_for_the_authenticated_user()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Create an order for the user
        $order = Order::factory()->create(['user_id' => $user->id]);

        // Call the show endpoint
        $response = $this->getJson("/api/orders/{$order->id}");

        // Assert that the order is retrieved successfully
        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'subtotal',
                'shipping_cost',
                'total',
                'status',
                'order_items',
                'transactions',
                'created_at',
                'updated_at',
            ]);
    }

    /** @test */
    public function it_returns_404_when_showing_a_non_existent_order()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Attempt to retrieve a non-existent order
        $response = $this->getJson('/api/orders/9999'); // Non-existent ID

        // Assert that a 404 error is returned
        $response->assertStatus(404)
            ->assertJson(['message' => 'Order not found']);
    }

    /** @test */
    public function it_can_update_an_order_with_valid_data()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Create an order for the user
        $order = Order::factory()->create(['user_id' => $user->id]);

        // Update data
        $updateData = [
            'subtotal' => '200.00',
            'shipping_cost' => '20.00',
            'total' => '220.00',
            'status' => 'Shipped',
        ];

        // Call the update endpoint
        $response = $this->putJson("/api/orders/{$order->id}", $updateData);

        // Assert that the order was updated successfully
        $response->assertStatus(200)
            ->assertJson([
                'id' => $order->id,
                'subtotal' => '200.00',
                'shipping_cost' => '20.00',
                'total' => '220.00',
                'status' => 'Shipped',
            ]);

        // Ensure the order was updated in the database
        $this->assertDatabaseHas('orders', ['subtotal' => '200.00', 'id' => $order->id]);
    }

    /** @test */
    public function it_cannot_update_an_order_with_invalid_data()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Create an order for the user
        $order = Order::factory()->create(['user_id' => $user->id]);

        // Invalid update data
        $invalidData = [
            'subtotal' => '',
            'shipping_cost' => '',
            'total' => '',
            'status' => '',
        ];

        // Call the update endpoint with invalid data
        $response = $this->putJson("/api/orders/{$order->id}", $invalidData);

        // Assert validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subtotal', 'shipping_cost', 'total', 'status']);
    }

    /** @test */
    public function it_can_delete_an_order_by_the_owner()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Create an order for the user
        $order = Order::factory()->create(['user_id' => $user->id]);

        // Call the delete endpoint
        $response = $this->deleteJson("/api/orders/{$order->id}");

        // Assert that the order was deleted successfully
        $response->assertStatus(204);

        // Ensure the order is no longer in the database
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    /** @test */
    public function it_returns_404_when_deleting_a_non_existent_order()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Attempt to delete a non-existent order
        $response = $this->deleteJson('/api/orders/9999'); // Non-existent ID

        // Assert that a 404 error is returned
        $response->assertStatus(404)
            ->assertJson(['message' => 'Order not found']);
    }

    /** @test */
    public function it_cannot_delete_an_order_that_does_not_belong_to_the_user()
    {
        // Create two users
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        // Create an order belonging to another user
        $order = Order::factory()->create(['user_id' => $anotherUser->id]);

        // Authenticate as the first user
        $this->actingAs($user, 'api');

        // Attempt to delete the order
        $response = $this->deleteJson("/api/orders/{$order->id}");

        // Assert that a 404 error is returned because the order does not belong to the authenticated user
        $response->assertStatus(404)
            ->assertJson(['message' => 'Order not found']);

        // Ensure the order still exists in the database
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }
}
