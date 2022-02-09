<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'position_id' => $this->faker->numberBetween(1, 10),
            'employment_date' => $this->faker->dateTimeThisMonth()->format('Y-m-d'),
            'phone_number' => $this->faker->numerify('+380 (##) ### ## ##'),
            'email' => $this->faker->email(),
            'salary' => $this->faker->randomElement(['$400,000', '$500,000', '$600,000', '$700,000']),
            'image' => "/images/placeholder.png",
            'admin_created_id' => 224,
            'admin_updated_id' => 224,
        ];
    }
}
