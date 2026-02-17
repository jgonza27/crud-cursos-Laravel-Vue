<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    /** @var string Estado: el curso acepta matrículas */
    const STATUS_ACTIVE = 'active';

    /** @var string Estado: borrador, no acepta matrículas */
    const STATUS_DRAFT = 'draft';

    /** @var string Estado: archivado, no acepta matrículas */
    const STATUS_ARCHIVED = 'archived';

    /** @var array<string> Todos los estados válidos */
    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_DRAFT,
        self::STATUS_ARCHIVED,
    ];

    protected $fillable = ['name', 'description', 'status'];

    /** @var array Valores por defecto para nuevos cursos */
    protected $attributes = [
        'status' => self::STATUS_ACTIVE,
    ];

    /**
     * Eventos del modelo.
     *
     * Al cambiar de 'active' a otro estado, se desmatriculan
     * automáticamente todos los estudiantes (course_id → null).
     */
    protected static function booted(): void
    {
        static::updating(function (Course $course) {
            $oldStatus = $course->getOriginal('status');
            $newStatus = $course->status;

            if ($oldStatus === self::STATUS_ACTIVE && $newStatus !== self::STATUS_ACTIVE) {
                $course->students()->update(['course_id' => null]);
            }
        });
    }

    /**
     * Comprobar si el curso está activo y acepta matrículas.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Estudiantes matriculados en este curso.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}