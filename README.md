
# Task Manager - Laravel Full Stack Application

A clean, intuitive, and reliable task management system allows teams to organize daily work, track task progress, and manage priorities efficiently.

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat-square&logo=laravel)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-06B6D4?style=flat-square&logo=tailwindcss)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Ready-4169E1?style=flat-square&logo=postgresql)

---

## 📋 Project Overview

The client required a simple task management system to help their team organize daily work. The core expectations were:
- A clean and intuitive interface to view and manage tasks.
- Ability to create, update, and remove tasks.
- A way to track task status (Pending, In Progress, Completed).
- Smooth interaction between frontend and backend.
- A reliable, structured backend system with validated data.
- Confidence that the system works correctly through automated testing.

## ✨ Key Features

- **Full CRUD Operations:** Create, Read, Update, and Delete tasks seamlessly.
- **Status Tracking:** Move tasks through `Pending`, `In Progress`, and `Completed` statuses with a quick-update dropdown.
- **Priority Levels:** Assign `Low`, `Medium`, or `High` priorities with color-coded badges.
- **Due Date & Overdue Tracking:** Set due dates; the system automatically flags overdue tasks in red.
- **Advanced Filtering:** Search tasks by title/description, filter by status, priority, or view only overdue tasks.
- **Dashboard Stats:** At-a-glance counters for Total, Pending, In Progress, Completed, and Overdue tasks.
- **Soft Deletes:** Tasks are soft-deleted to prevent accidental data loss.
- **RESTful API:** A complete versioned API (`/api/tasks`) with pagination, filtering, and proper JSON resources for frontend/mobile integration.
- **Comprehensive Testing:** 60+ Unit and Feature tests ensuring reliability and correct business logic.

## 🛠️ Tech Stack

- **Backend:** Laravel 11 (PHP 8.2+)
- **Frontend:** Blade Templates, Tailwind CSS v4, Alpine.js
- **Database:** MySQL (Local), PostgreSQL (Production/Render)
- **Testing:** PHPUnit (Feature & Unit Tests)
- **Deployment:** Docker, Render (Web Service + PostgreSQL)

---

## 🚀 Local Setup Instructions

Follow these steps to set up the project locally using XAMPP or any local PHP environment.

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js >= 20.x & NPM
- MySQL (or PostgreSQL)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/kazol196295/task-manager.git
   cd task-manager
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure Database**
   
   Open the `.env` file and update the database credentials.
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task_manager
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Run Migrations and Seed Database**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Build Frontend Assets**
   ```bash
   npm run build
   # Or for development: npm run dev
   ```

8. **Start the Server**
   ```bash
   php artisan serve
   ```
   Visit `http://127.0.0.1:8000` in your browser.

---

## 🧪 Testing

Reliability and testing are core focuses of this project. The suite includes **60+ tests** covering model logic, validation, web routing, and API endpoints.

Run the entire test suite:
```bash
php artisan test
```

### Test Structure
- **Unit Tests (`tests/Unit`):** Test the `Task` model in isolation (scopes, accessors like `is_overdue`, soft deletes).
- **Feature Web Tests (`tests/Feature/TaskTest`):** Test the web interface (viewing pages, creating tasks, validation errors, filtering).
- **Feature API Tests (`tests/Feature/TaskApiTest`):** Test the JSON API endpoints (pagination, creating/updating via JSON, 404 handling, status updates).

---

## 📡 API Endpoints

The application exposes a RESTful API prefixed with `/api`.

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| **GET** | `/api/tasks` | List all tasks (supports `?status=`, `?priority=`, `?search=`) |
| **POST** | `/api/tasks` | Create a new task |
| **GET** | `/api/tasks/{id}` | Get a specific task |
| **PUT/PATCH** | `/api/tasks/{id}` | Update a specific task |
| **DELETE** | `/api/tasks/{id}` | Soft delete a task |
| **PATCH** | `/api/tasks/{id}/status` | Quick update task status |
| **GET** | `/api/tasks/stats` | Get task counts by status |

---

## ☁️ Deployment (Render)

This project is containerized using Docker for seamless deployment on Render.

1. **Render PostgreSQL:** Create a PostgreSQL database on Render and copy the Internal Database URL.
2. **Render Web Service:** Create a new Web Service connected to the GitHub repo, select **Docker** as the runtime.
3. **Environment Variables:** Set the following in the Render Environment tab:
   - `APP_KEY`: Generate using `php artisan key:generate --show`
   - `APP_ENV`: `production`
   - `APP_DEBUG`: `false`
   - `APP_URL`: `https://your-app.onrender.com`
   - `DB_CONNECTION`: `pgsql`
   - `DATABASE_URL`: *(Paste the Internal Database URL from step 1)*
   - `PORT`: `80`

Migrations and seeding run automatically upon container startup via the `start.sh` script.

---

## 💡 Assumptions & Design Decisions

1. **Database Agnostic Design:** I assumed the application might be deployed on various hosting environments. Therefore, I configured `config/database.php` to parse the `DATABASE_URL` environment variable. This allows the app to run seamlessly on MySQL locally and PostgreSQL in production without changing any code or migrations.
2. **Soft Deletes over Hard Deletes:** I implemented Soft Deletes (`SoftDeletes` trait) instead of permanently removing records from the database. This ensures that accidental deletions by users can be recovered, aligning with the client's expectation of a "reliable" system.
3. **Separation of Concerns (Fat Models, Skinny Controllers):** 
   - **Scopes:** I moved filtering logic (status, priority, search, overdue) into model scopes (`scopeFilter`) to keep controllers clean and allow query reuse.
   - **Accessors:** Business logic like determining if a task is overdue (`getIsOverdueAttribute`) was placed in the model as an accessor, keeping views and controllers simple.
4. **Form Requests for Validation:** Instead of validating data directly in the controller, I created dedicated `StoreTaskRequest` and `UpdateTaskRequest` classes. This makes the validation rules reusable, easily testable, and keeps the controller focused on HTTP routing.
5. **API Resources:** I used `TaskResource` to format JSON responses. This prevents exposing sensitive or unnecessary database columns directly to the API consumer and provides a consistent data structure.
6. **Tailwind CSS v4:** Leveraged the latest Tailwind v4 along with the `@tailwindcss/vite` plugin for a modern, utility-first UI without the need for custom CSS files or heavy JavaScript frameworks.
7. **Quick Status Update:** Since changing a task's status is the most common action in a task manager, I created a dedicated route (`PATCH /tasks/{id}/status`) and an inline dropdown on the index page, so users don't have to navigate to the edit page just to mark a task complete.

---

## 🧪 Notes on Testing Approach

The client values reliability and expects core functionalities to be tested. I implemented a comprehensive testing suite using PHPUnit, focusing on both isolated logic and full HTTP lifecycle.

Run the entire test suite:
```bash
php artisan test
```

### Testing Strategy

1. **Unit Tests (Isolated Logic):**
   - Focused strictly on the `Task` model.
   - Tested custom scopes (`pending`, `completed`, `overdue`) to ensure they return the correct query constraints.
   - Tested accessors (e.g., `is_overdue` returns true only if past due date AND not completed).
   - Verified soft delete functionality and date casting.

2. **Feature Web Tests (User Journey):**
   - Simulated user interactions via HTTP requests to the web routes.
   - Tested that pages load successfully (200 OK) and display the correct Blade views.
   - Tested the full CRUD lifecycle: creating a task via POST, updating via PUT, and soft-deleting via DELETE.
   - Extensively tested form validation (missing required fields, invalid status/priority, past due dates) to ensure the application gracefully handles bad input.
   - Tested the filtering system to ensure search and status filters modify the view output correctly.

3. **Feature API Tests (JSON Endpoints):**
   - Tested the RESTful API endpoints (`/api/tasks`).
   - Verified correct HTTP status codes (201 for creation, 200 for OK, 404 for not found, 422 for validation errors).
   - Ensured the API returns properly structured JSON as defined in the `TaskResource`.
   - Tested API-specific features like pagination structure and the stats endpoint.

*This multi-layered approach ensures that whether a user is clicking through the UI or a mobile app is consuming the API, the system behaves as expected under normal and edge-case use.*
---

## 📄 License

This project is developed for the technical assessment at Qtec Solution Limited.


