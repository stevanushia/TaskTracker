# AI Usage Documentation 🤖

This project was built iteratively with the assistance of an AI Coding Partner (Gemini). Below is a summary of the tools, prompts, and workflows utilized during the development of the TaskTracker application.

## 🛠️ Tools & Technologies Assisted
* **AI Model:** Gemini (Acting as a "Coding Partner" persona)
* **Development Environment:** Laragon (Nginx, PHP 8.2, PostgreSQL 16), VS Code
* **Frontend Stack Generation:** Vite, Vue 3 (Composition API), TypeScript, Tailwind CSS v4, Pinia, Vue Router
* **Backend Stack Generation:** Laravel 12, Laravel Sanctum, PHPUnit
* **Testing:** PHPUnit (Backend SQLite in-memory), Vitest & Vue Test Utils (Frontend JSDOM)

## 📝 Key Prompts & Iterative Workflow

1. **Initial Setup & Database:**
   * *Prompt Context:* "I just initialized a new laravel 12 project. i attach my .env and stack that's required... PostgreSQL, Vue.js 3 + TypeScript..."
   * *AI Action:* Generated the base migrations, Eloquent models (with `$fillable` arrays and relationships), and initial seeders for Users, Projects, Tasks, and Categories based on the provided requirements.

2. **Authentication (Laravel Sanctum):**
   * *Prompt Context:* "Authentication. Login dengan email & password menggunakan Laravel Sanctum (PAT). Logout — hapus/revoke token aktif."
   * *AI Action:* Configured Sanctum API routing, created `AuthController`, built the Vue frontend login view (`LoginView.vue`), and set up the Axios interceptor (`api.ts`) to attach the Bearer token globally.

3. **Dashboard & Layout Development:**
   * *Prompt Context:* "create dashboard like in the attachment i gave you. Total project aktif. Total task belum selesai. Daftar task yang mendekati due date."
   * *AI Action:* Built the `MainLayout.vue` sidebar and `DashboardController` to aggregate database statistics and fetch upcoming tasks.

4. **Project CRU & Kanban Board (Drag-and-Drop):**
   * *Prompt Context:* "CRU project (nama, deskripsi, status). Pada project detail, tampilan list task dalam bentuk card yang bisa digeser/dipindahkan antar kategori."
   * *AI Action:* Implemented the Project table with filtering/search, and created the `ProjectDetailView.vue` with native HTML5 drag-and-drop for task cards across category columns.

5. **Global Task Management & Soft Deletes:**
   * *Prompt Context:* "CRUD task di dalam project... Read — list task per project & list task global... Delete — menggunakan soft delete."
   * *AI Action:* Built the `TaskView.vue` data table with global search and multi-dropdown filters. Implemented the `SoftDeletes` trait on the Task model alongside a custom `deleted_by` tracker, paired with a reusable `ConfirmDeleteModal.vue` component.

6. **Automated Testing & Bug Fixing:**
   * *Prompt Context:* "Minimal 1 Backend test (PHPUnit) and 1 Frontend test (Vitest/Vue Test Utils)."
   * *AI Action:* Guided the activation of the SQLite PHP extension in Laragon. Wrote `AuthTest.php` to verify API endpoint responses, and built a unit test (`Button.spec.ts`) for the reusable frontend button component testing dynamic props and slots.
