<template>
  <div class="page-container">
    <div class="page-header">
      <h1>Cursos</h1>
      <router-link to="/courses/create" class="btn btn-primary">
        <span>+</span> Nuevo Curso
      </router-link>
    </div>

    <div v-if="loading" class="loading-spinner">
      <div class="spinner"></div>
      <p>Cargando cursos...</p>
    </div>

    <div v-else-if="courses.length === 0" class="empty-state">
      <p>No hay cursos registrados</p>
      <router-link to="/courses/create" class="btn btn-primary">Crear primer curso</router-link>
    </div>

    <div v-else class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Estudiantes</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="course in courses" :key="course.id" :class="{ 'row-inactive': course.status !== 'active' }">
            <td class="id-cell">{{ course.id }}</td>
            <td class="name-cell">{{ course.name }}</td>
            <td class="desc-cell">{{ course.description || '—' }}</td>
            <td>
              <span class="badge" :class="statusBadgeClass(course.status)">
                {{ statusLabel(course.status) }}
              </span>
            </td>
            <td class="count-cell">
              <span class="badge">{{ course.students_count }}</span>
            </td>
            <td class="actions-cell">
              <router-link :to="`/courses/${course.id}/edit`" class="btn btn-sm btn-edit">
                Editar
              </router-link>
              <button @click="deleteCourse(course.id)" class="btn btn-sm btn-delete">
                Eliminar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="message" class="alert" :class="messageType">
      {{ message }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'CoursesList',
  data() {
    return {
      courses: [],
      loading: true,
      message: '',
      messageType: 'alert-success',
    };
  },
  mounted() {
    this.fetchCourses();
  },
  methods: {
    statusBadgeClass(status) {
      return {
        'badge-active': status === 'active',
        'badge-draft': status === 'draft',
        'badge-archived': status === 'archived',
      };
    },
    statusLabel(status) {
      const labels = { active: 'Activo', draft: 'Borrador', archived: 'Archivado' };
      return labels[status] || status;
    },
    async fetchCourses() {
      this.loading = true;
      try {
        const response = await fetch('/api/courses');
        this.courses = await response.json();
      } catch (error) {
        this.message = 'Error al cargar los cursos';
        this.messageType = 'alert-error';
      } finally {
        this.loading = false;
      }
    },
    async deleteCourse(id) {
      if (!confirm('¿Estás seguro de que quieres eliminar este curso? Se eliminarán también todos sus estudiantes.')) {
        return;
      }
      try {
        const response = await fetch(`/api/courses/${id}`, { method: 'DELETE' });
        if (response.ok) {
          this.message = 'Curso eliminado correctamente';
          this.messageType = 'alert-success';
          this.fetchCourses();
        }
      } catch (error) {
        this.message = 'Error al eliminar el curso';
        this.messageType = 'alert-error';
      }
      setTimeout(() => { this.message = ''; }, 3000);
    },
  },
};
</script>
