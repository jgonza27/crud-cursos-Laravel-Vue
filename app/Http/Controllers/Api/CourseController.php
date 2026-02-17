<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Listar todos los cursos con el conteo de estudiantes.
     */
    public function index()
    {
        $courses = Course::withCount('students')->get();
        return response()->json($courses);
    }

    /**
     * Crear un nuevo curso. El estado por defecto es 'active'.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['sometimes', Rule::in(Course::STATUSES)],
        ]);

        $course = Course::create($validated);
        return response()->json($course, 201);
    }

    /**
     * Mostrar un curso con sus estudiantes.
     */
    public function show(Course $course)
    {
        $course->load('students');
        return response()->json($course);
    }

    /**
     * Actualizar un curso.
     *
     * Si el estado cambia de 'active' a otro, los estudiantes se
     * desmatriculan automáticamente (lógica en el modelo Course).
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['sometimes', Rule::in(Course::STATUSES)],
        ]);

        $course->update($validated);
        return response()->json($course);
    }

    /**
     * Eliminar un curso y sus estudiantes asociados (por cascade).
     */
    public function destroy(Course $course)
    {
        $course->delete();
        return response()->json(null, 204);
    }
}