import { createRouter, createWebHistory } from 'vue-router';
import CoursesList from './components/CoursesList.vue';
import CourseForm from './components/CourseForm.vue';
import StudentsList from './components/StudentsList.vue';
import StudentForm from './components/StudentForm.vue';

const routes = [
    { path: '/', name: 'courses.index', component: CoursesList },
    { path: '/courses/create', name: 'courses.create', component: CourseForm },
    { path: '/courses/:id/edit', name: 'courses.edit', component: CourseForm },
    { path: '/students', name: 'students.index', component: StudentsList },
    { path: '/students/create', name: 'students.create', component: StudentForm },
    { path: '/students/:id/edit', name: 'students.edit', component: StudentForm },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
