<?php

namespace Database\Factories;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncidentFactory extends Factory
{
    protected $model = Incident::class;

    public function definition(): array
    {
        return [
            'user_panel_id' => User::factory(),
            'vehicle_number' => $this->faker->bothify('KR###??'),
            'vehicle_vin' => $this->faker->bothify('#################'),
            'vehicle_brand' => $this->faker->company,
            'vehicle_model' => $this->faker->word,
            'vehicle_type' => 'car',
            'description' => $this->faker->sentence,
            'status' => Incident::STATUS_OPEN,
        ];
    }
}
