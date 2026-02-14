<template>
  <div id="app-wrapper">
    <nav class="navbar">
      <div class="navbar-brand">
        <router-link to="/" class="brand-link">
          <span class="brand-text">Academia</span>
        </router-link>
      </div>
      <div class="navbar-links">
        <router-link to="/" class="nav-link" active-class="active">
          Cursos
        </router-link>
        <router-link to="/students" class="nav-link" active-class="active">
          Estudiantes
        </router-link>
      </div>
    </nav>
    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>

<script>
export default {
  name: 'App',
};
</script>
