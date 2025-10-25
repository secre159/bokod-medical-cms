<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Course;
use Illuminate\Support\Str;

class DepartmentCourseSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'Education' => [
                'Major in English',
                'Major in Filipino',
                'Social Science',
            ],
            'Criminology' => [
                'Criminology',
                'BPA',
            ],
            'CAT' => [
                'BSIT',
                'Entrep',
                'BIT',
            ],
        ];

        foreach ($map as $deptName => $courses) {
            $dept = Department::firstOrCreate(
                ['slug' => Str::slug($deptName)],
                ['name' => $deptName, 'active' => true]
            );

            foreach ($courses as $c) {
                Course::firstOrCreate(
                    ['name' => $c],
                    ['department_id' => $dept->id, 'active' => true]
                );
            }
        }
    }
}
