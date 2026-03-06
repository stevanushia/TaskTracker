# TaskTracker 🚀

TaskTracker is a modern, full-stack project and task management application built with Laravel 12 and Vue 3 (Composition API). It features a robust REST API, Laravel Sanctum authentication, and a responsive frontend with drag-and-drop Kanban boards.

## ✨ Features
* **Authentication:** Secure login and session management using Laravel Sanctum (Personal Access Tokens).
* **Dashboard:** Overview of active projects, incomplete tasks, and tasks approaching their due dates.
* **Project Management:** Create, read, and update projects (Active/Archived). Includes a detailed Kanban board view for managing project-specific tasks via drag-and-drop.
* **Global Task Management:** View, search, and filter all tasks across all projects.
* **Soft Deletes:** Tasks are safely soft-deleted, keeping a record of when and by whom they were deleted.

## 🛠️ Tech Stack
* **Backend:** Laravel 12, PHP 8.2+, PostgreSQL
* **Frontend:** Vue.js 3 (Composition API), TypeScript, Vite, Tailwind CSS v4, Pinia, Vue Router
* **Testing:** PHPUnit (Backend), Vitest & Vue Test Utils (Frontend)

## 📦 Prerequisites
Before you begin, ensure you have the following installed:
* PHP 8.2 or higher
* Composer
* Node.js & npm
* PostgreSQL
* (Optional but recommended) Laragon or Laravel Valet for local development

## 🚀 Installation Guide

1. **Clone the repository:**
   ```bash
   git clone https://github.com/stevanushia/TaskTracker.git
   cd TaskTracker
   ```

2. **Install Backend Dependencies:**
   ```bash
   composer install
   ```

3. **Install Frontend Dependencies:**
   ```bash
   npm install
   ```

4. **Environment Setup:**
   Copy the example environment file:
   ```bash
   cp .env.example .env
   ```
   Generate the application key:
   ```bash
   php artisan key:generate
   ```
   Open the `.env` file and configure your PostgreSQL database credentials:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=db_tasktracker
   DB_USERNAME=postgres
   DB_PASSWORD=
   ```

5. **Run Migrations and Seeders:**
   This will create the database tables and populate the default Admin user and Categories (Todo, InProgress, Testing, Done, Pending).
   ```bash
   php artisan migrate:fresh --seed
   ```

## 💻 How to Run the Application

Because this is a monorepo utilizing Vite for the frontend and Laravel for the backend, you need two terminal windows running simultaneously.

**Terminal 1 (Backend - PHP Server):**
```bash
php artisan serve
```

**Terminal 2 (Frontend - Vite Compiler):**
```bash
npm run dev
```

Open your browser and visit `http://localhost:8000` (or your configured local domain like `http://tasktracker.test`). 

**Default Login Credentials:**
* **Email:** `admin@example.com`
* **Password:** `password123`

## 🧪 Testing

**Backend Tests (PHPUnit):**
Ensures the API and authentication logic function correctly using an in-memory SQLite database.
```bash
php artisan test
```

**Frontend Tests (Vitest):**
Tests the Vue 3 reusable UI components.
```bash
npx vitest run
```
