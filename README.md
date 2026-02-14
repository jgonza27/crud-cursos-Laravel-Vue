# Academia — CRUD Cursos y Estudiantes

Aplicación web full stack para gestionar cursos y estudiantes.

- **Backend:** Laravel 11 (API REST)
- **Frontend:** Vue 3 (SPA con Vue Router)
- **Base de datos:** SQLite

## Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- npm

## Instalación

### 1. Clonar el proyecto

```bash
git clone https://github.com/jgonza27/crud-cursos-Laravel-Vue.git
cd crud-cursos
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Configurar el entorno

```bash
cp .env.example .env
php artisan key:generate
```

Edita el fichero `.env` y asegúrate de que la conexión a base de datos sea SQLite:

```
DB_CONNECTION=sqlite
```

### 4. Crear la base de datos y cargar datos de ejemplo

```bash
touch database/database.sqlite
php artisan migrate --seed
```

### 5. Instalar dependencias de Node

```bash
npm install
```

## Levantar la aplicación

Necesitas **dos terminales** abiertas:

**Terminal 1 — Servidor Laravel (API):**

```bash
php artisan serve
```

**Terminal 2 — Servidor Vite (frontend con hot reload):**

```bash
npm run dev
```

Abre el navegador en: **http://localhost:8000**

## Compilar para producción

Si quieres compilar los assets del frontend sin necesidad de Vite corriendo:

```bash
npm run build
```

Después solo necesitas el servidor Laravel:

```bash
php artisan serve
```

## Estructura del proyecto

```
app/
├── Models/
│   ├── Course.php          # Modelo Curso (hasMany Student)
│   └── Student.php         # Modelo Estudiante (belongsTo Course)
└── Http/Controllers/Api/
    ├── CourseController.php # CRUD API de cursos
    └── StudentController.php # CRUD API de estudiantes

database/
├── migrations/             # Tablas courses y students
└── seeders/                # Datos de ejemplo (3 cursos, 6 estudiantes)

resources/
├── css/app.css             # Estilos de la aplicación
├── js/
│   ├── App.vue             # Componente raíz (navbar + router-view)
│   ├── app.js              # Bootstrap de Vue
│   ├── router.js           # Rutas SPA (Vue Router)
│   └── components/
│       ├── CoursesList.vue  # Lista de cursos
│       ├── CourseForm.vue   # Crear/editar curso
│       ├── StudentsList.vue # Lista de estudiantes
│       └── StudentForm.vue  # Crear/editar estudiante
└── views/
    └── app.blade.php       # Vista principal (SPA shell)

routes/
├── api.php                 # Rutas API REST (/api/courses, /api/students)
└── web.php                 # Catch-all para SPA
```

## Endpoints API

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/courses` | Listar cursos (con nº de estudiantes) |
| POST | `/api/courses` | Crear curso |
| GET | `/api/courses/{id}` | Ver curso (con estudiantes) |
| PUT | `/api/courses/{id}` | Actualizar curso |
| DELETE | `/api/courses/{id}` | Eliminar curso |
| GET | `/api/students` | Listar estudiantes (con curso) |
| POST | `/api/students` | Crear estudiante |
| GET | `/api/students/{id}` | Ver estudiante |
| PUT | `/api/students/{id}` | Actualizar estudiante |
| DELETE | `/api/students/{id}` | Eliminar estudiante |
