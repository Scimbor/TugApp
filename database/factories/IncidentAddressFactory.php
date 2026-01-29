<?php

namespace Database\Factories;

use App\Models\IncidentAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncidentAddressFactory extends Factory
{
    protected $model = IncidentAddress::class;

    public function definition(): array
    {
        return [
            'street' => $this->faker->streetName,
            'house_number' => $this->faker->buildingNumber,
            'apartment_number' => $this->faker->optional()->randomDigit,
            'city' => $this->faker->city,
            'zip' => $this->faker->postcode,
        ];
    }
}
