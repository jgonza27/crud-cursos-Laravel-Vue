<template>
  <div class="page-container">
    <div class="page-header">
      <h1>Estudiantes</h1>
      <router-link to="/students/create" class="btn btn-primary">
        <span>+</span> Nuevo Estudiante
      </router-link>
    </div>

    <div v-if="loading" class="loading-spinner">
      <div class="spinner"></div>
      <p>Cargando estudiantes...</p>
    </div>

    <div v-else-if="students.length === 0" class="empty-state">
      <p>No hay estudiantes registrados</p>
      <router-link to="/students/create" class="btn btn-primary">Registrar primer estudiante</router-link>
    </div>

    <div v-else class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Curso</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="student in students" :key="student.id">
            <td class="id-cell">{{ student.id }}</td>
            <td class="name-cell">{{ student.name }}</td>
            <td class="email-cell">{{ student.email }}</td>
            <td class="course-cell">
              <span v-if="student.course" class="badge badge-course">{{ student.course.name }}</span>
              <span v-else class="badge badge-no-course">Sin curso</span>
            </td>
            <td class="actions-cell">
              <router-link :to="`/students/${student.id}/edit`" class="btn btn-sm btn-edit">
                Editar
              </router-link>
              <button @click="deleteStudent(student.id)" class="btn btn-sm btn-delete">
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
  name: 'StudentsList',
  data() {
    return {
      students: [],
      loading: true,
      message: '',
      messageType: 'alert-success',
    };
  },
  mounted() {
    this.fetchStudents();
  },
  methods: {
    async fetchStudents() {
      this.loading = true;
      try {
        const response = await fetch('/api/students');
        this.students = await response.json();
      } catch (error) {
        this.message = 'Error al cargar los estudiantes';
        this.messageType = 'alert-error';
      } finally {
        this.loading = false;
      }
    },
    async deleteStudent(id) {
      if (!confirm('¿Estás seguro de que quieres eliminar este estudiante?')) {
        return;
      }
      try {
        const response = await fetch(`/api/students/${id}`, { method: 'DELETE' });
        if (response.ok) {
          this.message = 'Estudiante eliminado correctamente';
          this.messageType = 'alert-success';
          this.fetchStudents();
        }
      } catch (error) {
        this.message = 'Error al eliminar el estudiante';
        this.messageType = 'alert-error';
      }
      setTimeout(() => { this.message = ''; }, 3000);
    },
  },
};
</script>
