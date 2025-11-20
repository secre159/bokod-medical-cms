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
            'CED' => [
                'Secondary',
                'Bsed',
                'Filipino',
                'Math',
                'Social studies',
                'btvted',
                'btled',
                'bee',
            ],
            'BPA' => [
                'BPA',
            ],
            'CRIM' => [
                'CRIM',
            ],
            'CAT/TECHSOC' => [
                'BIT',
                'BSIT',
                'ENTREP',
            ],
        ];

        foreach ($map as $deptName => $courses) {
            $dept = Department::firstOrCreate(
                ['slug' => Str::slug($deptName)],
                ['name' => $deptName, 'active' => true]
            );

            foreach ($courses as $c) {
                Course::updateOrCreate(
                    ['name' => $c],
                    ['department_id' => $dept->id, 'active' => true]
                );
            }
        }
    }
}
