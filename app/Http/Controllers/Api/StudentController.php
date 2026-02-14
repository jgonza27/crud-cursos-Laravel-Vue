<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('course')->get();
        return response()->json($students);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'course_id' => 'required|exists:courses,id',
        ]);

        $student = Student::create($validated);
        $student->load('course');
        return response()->json($student, 201);
    }

    public function show(Student $student)
    {
        $student->load('course');
        return response()->json($student);
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'course_id' => 'required|exists:courses,id',
        ]);

        $student->update($validated);
        $student->load('course');
        return response()->json($student);
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return response()->json(null, 204);
    }
}
