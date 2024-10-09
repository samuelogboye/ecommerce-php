<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_retrieve_all_banners()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        Banner::factory()->count(3)->create();

        $response = $this->getJson('/api/banners');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'content', 'location'],
                ],
                'count',
            ]);

        $this->assertEquals(3, $response['count']);
    }

    /** @test */
    public function it_can_store_a_new_banner_with_valid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/banners', [
            'content' => 'Black Friday Sale',
            'location' => 'Homepage',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'content',
                'location',
                'created_at',
                'updated_at',
            ]);
    }

    /** @test */
    public function it_cannot_store_a_new_banner_with_invalid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/banners', [
            'content' => '', // Missing content
            'location' => '', // Missing location
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content', 'location']);
    }

    /** @test */
    public function it_can_show_a_banner_by_id()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $banner = Banner::factory()->create();

        $response = $this->getJson("/api/banners/{$banner->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'content',
                'location',
                'created_at',
                'updated_at',
            ]);
    }

    /** @test */
    public function it_returns_404_if_banner_not_found()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->getJson('/api/banners/9999'); // Non-existent ID

        $response->assertStatus(404)
            ->assertJson(['message' => 'Banner not found']);
    }

    /** @test */
    public function it_can_update_a_banner_with_valid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $banner = Banner::factory()->create([
            'content' => 'Old Content',
            'location' => 'Old Location',
        ]);

        $response = $this->putJson("/api/banners/{$banner->id}", [
            'content' => 'Updated Content',
            'location' => 'Updated Location',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $banner->id,
                'content' => 'Updated Content',
                'location' => 'Updated Location',
            ]);
    }

    /** @test */
    public function it_cannot_update_a_banner_with_invalid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $banner = Banner::factory()->create();

        $response = $this->putJson("/api/banners/{$banner->id}", [
            'content' => '', // Invalid data
            'location' => '', // Invalid data
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content', 'location']);
    }

    /** @test */
    public function it_can_delete_a_banner()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $banner = Banner::factory()->create();

        $response = $this->deleteJson("/api/banners/{$banner->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('banners', ['id' => $banner->id]);
    }

    /** @test */
    public function it_returns_404_when_trying_to_delete_a_non_existent_banner()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Attempt to delete a non-existent banner
        $response = $this->deleteJson('/api/banners/9999'); // Non-existent ID

        $response->assertStatus(404)
            ->assertJson(['message' => 'Banner not found']);
    }
}
