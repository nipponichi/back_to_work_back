<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Student;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => substr($this->faker->name(), 0, 32),
            'phone' => substr($this->faker->phoneNumber(), 0, 16),
            'age' => $this->faker->optional()->numberBetween(18, 60),
            'password' => bcrypt('12345678'),
            'email' => $this->faker->unique()->safeEmail(),
            'gender' => $this->faker->randomElement(['m', 'f']),
        ];
    }
}
