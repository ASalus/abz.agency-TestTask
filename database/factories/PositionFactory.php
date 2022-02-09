<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'position_name' => $this->faker->unique()->randomElement([
                'Customer Security Engineer', 'Net Developer', 'PHP Developer', 'Software Engineer',
                'Front-End Web Developer', 'Web Designer', 'Full-Stack Developer', 'Web Analyst', 'UX/UI Developer',
                'Back-End Web Developer'
            ]),
        ];
    }
}
