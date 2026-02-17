<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_is_created_with_given_status(): void
    {
        $response = $this->postJson('/api/courses', [
            'name' => 'Test Course',
            'description' => 'A test course',
            'status' => 'draft',
        ]);

        $response->assertStatus(201);
        $this->assertEquals('draft', $response->json('status'));
    }

    public function test_course_default_status_is_active(): void
    {
        $response = $this->postJson('/api/courses', [
            'name' => 'Test Course',
            'description' => 'A test course',
        ]);

        $response->assertStatus(201);
        $this->assertEquals('active', $response->json('status'));
    }

    public function test_student_can_enroll_in_active_course(): void
    {
        $course = Course::create([
            'name' => 'Active Course',
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/students', [
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'course_id' => $course->id,
        ]);

        $response->assertStatus(201);
        $this->assertEquals($course->id, $response->json('course_id'));
    }

    public function test_student_cannot_enroll_in_draft_course(): void
    {
        $course = Course::create([
            'name' => 'Draft Course',
            'status' => 'draft',
        ]);

        $response = $this->postJson('/api/students', [
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'course_id' => $course->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('course_id');
    }

    public function test_student_cannot_enroll_in_archived_course(): void
    {
        $course = Course::create([
            'name' => 'Archived Course',
            'status' => 'archived',
        ]);

        $response = $this->postJson('/api/students', [
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'course_id' => $course->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('course_id');
    }

    public function test_students_are_unenrolled_when_course_is_archived(): void
    {
        $course = Course::create([
            'name' => 'Active Course',
            'status' => 'active',
        ]);

        $student1 = Student::create([
            'name' => 'Student 1',
            'email' => 's1@example.com',
            'course_id' => $course->id,
        ]);

        $student2 = Student::create([
            'name' => 'Student 2',
            'email' => 's2@example.com',
            'course_id' => $course->id,
        ]);

        $this->putJson("/api/courses/{$course->id}", [
            'name' => 'Active Course',
            'status' => 'archived',
        ]);

        $this->assertNull($student1->fresh()->course_id);
        $this->assertNull($student2->fresh()->course_id);
    }

    public function test_students_are_unenrolled_when_course_becomes_draft(): void
    {
        $course = Course::create([
            'name' => 'Active Course',
            'status' => 'active',
        ]);

        $student = Student::create([
            'name' => 'Student',
            'email' => 's@example.com',
            'course_id' => $course->id,
        ]);

        $this->putJson("/api/courses/{$course->id}", [
            'name' => 'Active Course',
            'status' => 'draft',
        ]);

        $this->assertNull($student->fresh()->course_id);
    }

    public function test_students_are_not_deleted_when_unenrolled(): void
    {
        $course = Course::create([
            'name' => 'Active Course',
            'status' => 'active',
        ]);

        $student = Student::create([
            'name' => 'Student',
            'email' => 's@example.com',
            'course_id' => $course->id,
        ]);

        $this->putJson("/api/courses/{$course->id}", [
            'name' => 'Active Course',
            'status' => 'archived',
        ]);

        $this->assertDatabaseHas('students', ['id' => $student->id]);
        $this->assertNull($student->fresh()->course_id);
    }

    public function test_student_cannot_be_reassigned_to_inactive_course(): void
    {
        $activeCourse = Course::create([
            'name' => 'Active Course',
            'status' => 'active',
        ]);

        $draftCourse = Course::create([
            'name' => 'Draft Course',
            'status' => 'draft',
        ]);

        $student = Student::create([
            'name' => 'Student',
            'email' => 's@example.com',
            'course_id' => $activeCourse->id,
        ]);

        $response = $this->putJson("/api/students/{$student->id}", [
            'name' => 'Student',
            'email' => 's@example.com',
            'course_id' => $draftCourse->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('course_id');
    }

    public function test_invalid_status_is_rejected(): void
    {
        $response = $this->postJson('/api/courses', [
            'name' => 'Test Course',
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('status');
    }
}