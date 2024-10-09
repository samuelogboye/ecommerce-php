<?php

namespace Tests\Feature;

use App\Models\AddressInfo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressInfoControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_access_the_address_info_index_route(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->get('/api/address_infos');
        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_store_a_new_address_info_with_valid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/address_infos', [
            'address_1' => '123 Main St',
            'address_2' => 'Apt 4',
            'city' => 'New York',
            'state_province' => 'NY',
            'country' => 'USA',
            'zipcode' => '10001',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'address_1', 'city']);
    }

    /** @test */
    public function it_cannot_store_address_info_with_invalid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/address_infos', [
            'address_1' => '', // Required field is missing
            'city' => 'New York',
            'state_province' => 'NY',
            'country' => 'USA',
            'zipcode' => '10001',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['address_1']);
    }

    /** @test */
    public function it_can_show_an_address_info_belonging_to_the_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $addressInfo = AddressInfo::factory()->create(['user_id' => $user->id]);

        $response = $this->get("/api/address_infos/{$addressInfo->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $addressInfo->id]);
    }

    /** @test */
    public function it_cannot_show_address_info_of_another_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $addressInfo = AddressInfo::factory()->create(); // Different user

        $response = $this->get("/api/address_infos/{$addressInfo->id}");

        $response->assertStatus(404)
            ->assertJson(['message' => 'AddressInfo not found or access denied']);
    }

    /** @test */
    public function it_can_update_an_address_info_with_valid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $addressInfo = AddressInfo::factory()->create(['user_id' => $user->id]);

        $response = $this->putJson("/api/address_infos/{$addressInfo->id}", [
            'address_1' => '456 New Address',
            'city' => 'Los Angeles',
            'state_province' => 'CA',
            'country' => 'USA',
            'zipcode' => '90001',
        ]);

        $response->assertStatus(200)
            ->assertJson(['address_1' => '456 New Address']);
    }

    /** @test */
    public function it_cannot_update_address_info_of_another_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $addressInfo = AddressInfo::factory()->create(); // Another user

        $response = $this->putJson("/api/address_infos/{$addressInfo->id}", [
            'address_1' => 'New Address',
            'city' => 'New York',
        ]);

        $response->assertStatus(404)
            ->assertJson(['message' => 'AddressInfo not found or access denied']);
    }

    /** @test */
    public function it_can_delete_an_address_info()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $addressInfo = AddressInfo::factory()->create(['user_id' => $user->id]);

        $response = $this->delete("/api/address_infos/{$addressInfo->id}");

        $response->assertStatus(204);
    }

    /** @test */
    public function it_cannot_delete_address_info_of_another_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $addressInfo = AddressInfo::factory()->create(); // Another user

        $response = $this->delete("/api/address_infos/{$addressInfo->id}");

        $response->assertStatus(404)
            ->assertJson(['message' => 'AddressInfo not found or access denied']);
    }
}
