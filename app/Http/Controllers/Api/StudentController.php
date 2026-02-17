<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Listar todos los estudiantes con su curso asociado.
     */
    public function index()
    {
        $students = Student::with('course')->get();
        return response()->json($students);
    }

    /**
     * Registrar un nuevo estudiante.
     *
     * Solo se permite matricular en cursos con estado 'active'.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($validated['course_id']);

        if (!$course->isActive()) {
            return response()->json([
                'message' => 'El curso seleccionado no está activo.',
                'errors' => [
                    'course_id' => ['El curso seleccionado no está activo. Solo se pueden matricular estudiantes en cursos activos.'],
                ],
            ], 422);
        }

        $student = Student::create($validated);
        $student->load('course');
        return response()->json($student, 201);
    }

    /**
     * Mostrar un estudiante con su curso.
     */
    public function show(Student $student)
    {
        $student->load('course');
        return response()->json($student);
    }

    /**
     * Actualizar un estudiante.
     *
     * Si se cambia el curso, solo se permite asignarlo a un curso activo.
     * Se permite enviar course_id como null (estudiante sin curso).
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        if (!empty($validated['course_id'])) {
            $course = Course::findOrFail($validated['course_id']);

            if (!$course->isActive()) {
                return response()->json([
                    'message' => 'El curso seleccionado no está activo.',
                    'errors' => [
                        'course_id' => ['El curso seleccionado no está activo. Solo se pueden matricular estudiantes en cursos activos.'],
                    ],
                ], 422);
            }
        }

        $student->update($validated);
        $student->load('course');
        return response()->json($student);
    }

    /**
     * Eliminar un estudiante.
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return response()->json(null, 204);
    }
}