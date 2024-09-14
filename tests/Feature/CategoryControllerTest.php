<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_retrieve_all_categories()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         '*' => ['id', 'name', 'sub_categories', 'products']
                     ],
                     'count'
                 ]);

        $this->assertEquals(3, $response['count']);
    }

    /** @test */
    public function it_can_store_a_new_category_with_valid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/categories', [
            'name' => 'Electronics'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id',
                     'name',
                     'created_at',
                     'updated_at',
                 ]);
    }

    /** @test */
    public function it_cannot_store_a_new_category_with_invalid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/categories', [
            'name' => '', // Missing name
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function it_cannot_store_a_category_with_duplicate_name()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        Category::factory()->create(['name' => 'Electronics']);

        $response = $this->postJson('/api/categories', [
            'name' => 'Electronics'
        ]);

        $response->assertStatus(422)
                 ->assertJson(['error' => 'Category name already exists']);
    }

    /** @test */
    public function it_can_show_a_category_by_id()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $category = Category::factory()->create();

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'name',
                     'sub_categories',
                     'products',
                     'created_at',
                     'updated_at'
                 ]);
    }

    /** @test */
    public function it_returns_404_if_category_not_found()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->getJson('/api/categories/9999'); // Non-existent ID

        $response->assertStatus(404)
                 ->assertJson(['message' => 'Category not found']);
    }

    /** @test */
    public function it_can_update_a_category_with_valid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $category = Category::factory()->create([
            'name' => 'Old Category'
        ]);

        $response = $this->putJson("/api/categories/{$category->id}", [
            'name' => 'Updated Category'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $category->id,
                     'name' => 'Updated Category'
                 ]);
    }

    /** @test */
    public function it_cannot_update_a_category_with_invalid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $category = Category::factory()->create();

        $response = $this->putJson("/api/categories/{$category->id}", [
            'name' => '', // Invalid data
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function it_can_delete_a_category()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function it_returns_404_when_trying_to_delete_a_non_existent_category()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->deleteJson('/api/categories/9999'); // Non-existent ID

        $response->assertStatus(404)
                 ->assertJson(['message' => 'Category not found']);
    }
}

