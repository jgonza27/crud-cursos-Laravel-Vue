<template>
  <div class="page-container">
    <div class="page-header">
      <h1>{{ isEditing ? 'Editar Curso' : 'Nuevo Curso' }}</h1>
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

      <div class="form-group">
        <label for="status">Estado</label>
        <select
          id="status"
          v-model="form.status"
          class="form-control"
          required
        >
          <option value="active">Activo</option>
          <option value="draft">Borrador</option>
          <option value="archived">Archivado</option>
        </select>
        <span v-if="errors.status" class="error-text">{{ errors.status[0] }}</span>
        <div v-if="showStatusWarning" class="alert alert-warning" style="margin-top: 0.5rem;">
          ⚠️ Al cambiar el estado a "{{ statusLabel(form.status) }}", todos los estudiantes matriculados serán desmatriculados automáticamente.
        </div>
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
        status: 'active',
      },
      originalStatus: 'active',
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
    showStatusWarning() {
      return this.isEditing && this.originalStatus === 'active' && this.form.status !== 'active';
    },
  },
  mounted() {
    if (this.isEditing) {
      this.fetchCourse();
    }
  },
  methods: {
    statusLabel(status) {
      const labels = { active: 'Activo', draft: 'Borrador', archived: 'Archivado' };
      return labels[status] || status;
    },
    async fetchCourse() {
      try {
        const response = await fetch(`/api/courses/${this.$route.params.id}`);
        const course = await response.json();
        this.form.name = course.name;
        this.form.description = course.description || '';
        this.form.status = course.status || 'active';
        this.originalStatus = course.status || 'active';
      } catch (error) {
        this.message = 'Error al cargar el curso';
        this.messageType = 'alert-error';
      }
    },
    async submitForm() {
      if (this.showStatusWarning) {
        if (!confirm('Al cambiar el estado, todos los estudiantes matriculados serán desmatriculados. ¿Deseas continuar?')) {
          return;
        }
      }
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
