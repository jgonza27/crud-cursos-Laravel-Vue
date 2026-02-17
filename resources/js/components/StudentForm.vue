<template>
  <div class="page-container">
    <div class="page-header">
      <h1>{{ isEditing ? 'Editar Estudiante' : 'Nuevo Estudiante' }}</h1>
      <router-link to="/students" class="btn btn-secondary">← Volver</router-link>
    </div>

    <form @submit.prevent="submitForm" class="form-card">
      <div class="form-group">
        <label for="name">Nombre del estudiante</label>
        <input
          type="text"
          id="name"
          v-model="form.name"
          class="form-control"
          placeholder="Ej: María García"
          required
        />
        <span v-if="errors.name" class="error-text">{{ errors.name[0] }}</span>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input
          type="email"
          id="email"
          v-model="form.email"
          class="form-control"
          placeholder="Ej: maria@example.com"
          required
        />
        <span v-if="errors.email" class="error-text">{{ errors.email[0] }}</span>
      </div>

      <div class="form-group">
        <label for="course_id">Curso</label>
        <select
          id="course_id"
          v-model="form.course_id"
          class="form-control"
          :required="!isEditing"
        >
          <option value="" disabled>Selecciona un curso</option>
          <option
            v-for="course in availableCourses"
            :key="course.id"
            :value="course.id"
          >
            {{ course.name }}
          </option>
        </select>
        <span v-if="currentCourseInactive" class="status-hint">
          ⚠️ El curso actual no está activo. Selecciona un curso activo para rematricular.
        </span>
        <span v-if="!isEditing && activeCourses.length === 0 && !loadingCourses" class="status-hint">
          No hay cursos activos disponibles. Crea o activa un curso primero.
        </span>
        <span v-if="errors.course_id" class="error-text">{{ errors.course_id[0] }}</span>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary" :disabled="submitting || (!isEditing && activeCourses.length === 0)">
          {{ submitting ? 'Guardando...' : (isEditing ? 'Actualizar Estudiante' : 'Registrar Estudiante') }}
        </button>
        <router-link to="/students" class="btn btn-secondary">Cancelar</router-link>
      </div>
    </form>

    <div v-if="message" class="alert" :class="messageType">
      {{ message }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'StudentForm',
  data() {
    return {
      form: {
        name: '',
        email: '',
        course_id: '',
      },
      courses: [],
      loadingCourses: true,
      originalCourseId: null,
      errors: {},
      submitting: false,
      message: '',
      messageType: 'alert-success',
    };
  },
  computed: {
    isEditing() {
      return !!this.$route.params.id;
    },
    activeCourses() {
      return this.courses.filter(c => c.status === 'active');
    },
    availableCourses() {
      if (this.isEditing) {
        // When editing, show active courses + the currently assigned course (even if inactive)
        return this.courses.filter(c => c.status === 'active' || c.id === this.originalCourseId);
      }
      return this.activeCourses;
    },
    currentCourseInactive() {
      if (!this.isEditing || !this.originalCourseId) return false;
      const currentCourse = this.courses.find(c => c.id === this.originalCourseId);
      return currentCourse && currentCourse.status !== 'active';
    },
  },
  mounted() {
    this.fetchCourses();
    if (this.isEditing) {
      this.fetchStudent();
    }
  },
  methods: {
    async fetchCourses() {
      this.loadingCourses = true;
      try {
        const response = await fetch('/api/courses');
        this.courses = await response.json();
      } catch (error) {
        this.message = 'Error al cargar los cursos';
        this.messageType = 'alert-error';
      } finally {
        this.loadingCourses = false;
      }
    },
    async fetchStudent() {
      try {
        const response = await fetch(`/api/students/${this.$route.params.id}`);
        const student = await response.json();
        this.form.name = student.name;
        this.form.email = student.email;
        this.form.course_id = student.course_id || '';
        this.originalCourseId = student.course_id;
      } catch (error) {
        this.message = 'Error al cargar el estudiante';
        this.messageType = 'alert-error';
      }
    },
    async submitForm() {
      this.submitting = true;
      this.errors = {};
      this.message = '';
      try {
        const url = this.isEditing
          ? `/api/students/${this.$route.params.id}`
          : '/api/students';
        const method = this.isEditing ? 'PUT' : 'POST';

        const payload = { ...this.form };
        if (payload.course_id === '') {
          payload.course_id = null;
        }

        const response = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify(payload),
        });

        if (response.status === 422) {
          const data = await response.json();
          this.errors = data.errors || {};
          this.message = data.message || 'Error de validación';
          this.messageType = 'alert-error';
          return;
        }

        if (response.ok || response.status === 201) {
          this.$router.push({ name: 'students.index' });
        }
      } catch (error) {
        this.message = 'Error al guardar el estudiante';
        this.messageType = 'alert-error';
      } finally {
        this.submitting = false;
      }
    },
  },
};
</script>
