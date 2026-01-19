<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Personnel\ErpPerson;

class ErpPersonFactory extends Factory
{
    protected $model = ErpPerson::class;

    public function definition()
    {
        return [
            'MRID' => 'EMP' . $this->faker->unique()->randomNumber(6),
            'NAME' => $this->faker->name,
            'FIRST_NAME' => $this->faker->firstName,
            'LAST_NAME' => $this->faker->lastName,
            'GENDER' => $this->faker->randomElement(['Male', 'Female']),
            'BIRTH_DATE_TIME' => $this->faker->dateTimeBetween('-60 years', '-20 years'),
            'NATIONALITY' => 'South African',
            'EMAIL' => $this->faker->unique()->safeEmail,
        ];
    }
}