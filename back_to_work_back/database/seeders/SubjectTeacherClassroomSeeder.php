<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\Student;

class SubjectTeacherClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Teacher::all()->each(function ($teacher) {
            Subject::factory()->count(2)->create(['teacher_id' => $teacher->id]);
            Classroom::factory()->count(1)->create(['teacher_id' => $teacher->id]);
        });

        $students = Student::all();

        Subject::all()->each(function ($subject) use ($students) {
            $subject->students()->attach($students->random(5)->pluck('id')->toArray());
        });
    }
}
