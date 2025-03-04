<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Teacher;
use App\Models\Subject;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{

    protected $model = Subject::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subjects = [
            'Maths', 'History', 'Science', 'Database', 
            'Development', 'Sexual Education', 'Foreign Languaje', 
            'Gymnastics', 'Philosophy', 'Music'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($subjects),
            'teacher_id' => Teacher::factory(), 
        ];
    }
}
