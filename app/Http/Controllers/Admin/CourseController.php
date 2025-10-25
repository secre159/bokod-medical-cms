<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('department')->orderBy('name')->paginate(20);
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $departments = Department::where('active', true)->orderBy('name')->get();
        return view('admin.courses.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150','unique:courses,name'],
            'code' => ['nullable','string','max:50'],
            'department_id' => ['required','exists:departments,id'],
            'active' => ['nullable','boolean'],
        ]);

        $course = Course::create([
            'name' => $data['name'],
            'code' => $data['code'] ?? null,
            'department_id' => $data['department_id'],
            'active' => $request->boolean('active', true),
        ]);

        return redirect()->route('courses.index')->with('success', 'Course "'.$course->name.'" created successfully.');
    }
}
