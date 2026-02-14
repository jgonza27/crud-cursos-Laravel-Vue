<template>
  <div class="page-container">
    <div class="page-header">
      <h1>{{ isEditing ? '✏️ Editar Curso' : '➕ Nuevo Curso' }}</h1>
      <router-link to="/" class="btn btn-secondary">← Volver</router-link>
    </div>

    <form @submit.prevent="submitForm" class="form-card">
      <div class="form-group">
        <label for="name">Nombre del curso</label>
        <input
          type="text"
          id="name"
          v-model="form.name"
          class="form-control"
          placeholder="Ej: Desarrollo Web"
          required
        />
        <span v-if="errors.name" class="error-text">{{ errors.name[0] }}</span>
      </div>

      <div class="form-group">
        <label for="description">Descripción</label>
        <textarea
          id="description"
          v-model="form.description"
          class="form-control"
          placeholder="Describe el contenido del curso..."
          rows="4"
        ></textarea>
        <span v-if="errors.description" class="error-text">{{ errors.description[0] }}</span>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary" :disabled="submitting">
          {{ submitting ? 'Guardando...' : (isEditing ? 'Actualizar Curso' : 'Crear Curso') }}
        </button>
        <router-link to="/" class="btn btn-secondary">Cancelar</router-link>
      </div>
    </form>

    <div v-if="message" class="alert" :class="messageType">
      {{ message }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'CourseForm',
  data() {
    return {
      form: {
        name: '',
        description: '',
      },
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
  },
  mounted() {
    if (this.isEditing) {
      this.fetchCourse();
    }
  },
  methods: {
    async fetchCourse() {
      try {
        const response = await fetch(`/api/courses/${this.$route.params.id}`);
        const course = await response.json();
        this.form.name = course.name;
        this.form.description = course.description || '';
      } catch (error) {
        this.message = 'Error al cargar el curso';
        this.messageType = 'alert-error';
      }
    },
    async submitForm() {
      this.submitting = true;
      this.errors = {};
      try {
        const url = this.isEditing
          ? `/api/courses/${this.$route.params.id}`
          : '/api/courses';
        const method = this.isEditing ? 'PUT' : 'POST';

        const response = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify(this.form),
        });

        if (response.status === 422) {
          const data = await response.json();
          this.errors = data.errors;
          return;
        }

        if (response.ok || response.status === 201) {
          this.$router.push({ name: 'courses.index' });
        }
      } catch (error) {
        this.message = 'Error al guardar el curso';
        this.messageType = 'alert-error';
      } finally {
        this.submitting = false;
      }
    },
  },
};
</script>
