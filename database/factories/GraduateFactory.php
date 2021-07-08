<?php

namespace Database\Factories;

use App\Models\Graduate;
use Illuminate\Database\Eloquent\Factories\Factory;

class GraduateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Graduate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "firstName" => $this->faker->name,
            "lastName" => $this->faker->name,
            "registrationNumber" => $this->faker->postcode
        ];
    }
}
