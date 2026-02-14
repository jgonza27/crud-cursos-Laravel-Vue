<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Student;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $daw = Course::create(['name' => 'DAW', 'description' => 'Desarrollo de Aplicaciones Web']);
        $dam = Course::create(['name' => 'DAM', 'description' => 'Desarrollo de Aplicaciones Multiplataforma']);
        $asir = Course::create(['name' => 'ASIR', 'description' => 'Administración de Sistemas Informáticos en Red']);

        Student::create(['name' => 'Ana García', 'email' => 'ana@example.com', 'course_id' => $daw->id]);
        Student::create(['name' => 'Carlos López', 'email' => 'carlos@example.com', 'course_id' => $daw->id]);
        Student::create(['name' => 'María Fernández', 'email' => 'maria@example.com', 'course_id' => $dam->id]);
        Student::create(['name' => 'Pedro Martín', 'email' => 'pedro@example.com', 'course_id' => $dam->id]);
        Student::create(['name' => 'Lucía Sánchez', 'email' => 'lucia@example.com', 'course_id' => $asir->id]);
        Student::create(['name' => 'Javier Ruiz', 'email' => 'javier@example.com', 'course_id' => $asir->id]);
    }
}
