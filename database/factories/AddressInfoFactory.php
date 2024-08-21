<?php

namespace Database\Factories;

use App\Models\AddressInfo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressInfoFactory extends Factory
{
    protected $model = AddressInfo::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'address_1' => $this->faker->streetAddress,
            'address_2' => $this->faker->secondaryAddress,
            'country' => $this->faker->country,
            'state_province' => $this->faker->state,
            'city' => $this->faker->city,
            'zipcode' => $this->faker->postcode,
        ];
    }
}
