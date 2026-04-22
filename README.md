
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

The client values reliability and expects core functionalities to be tested. I implemented a comprehensive testing suite using PHPUnit, focusing on both isolated business logic and full HTTP lifecycle simulations.

Run the entire test suite:
```bash
php artisan test
```

### 1. TaskTest.php (Feature Tests)
This file tests the application from the user's perspective, simulating HTTP requests to ensure web features like page visits, form submissions, and filtering work correctly.

**── View / Index Tests (Viewing Task Lists & Pages) ──**
* **test_can_view_tasks_index_page:** Checks if the task list page (tasks.index route) loads successfully and receives the task data.
* **test_index_displays_stats_correctly:** Ensures the dashboard statistics (e.g., Total 3, Pending 1, Completed 1) are calculated and displayed accurately.
* **test_index_can_filter_by_status:** Verifies that when a user filters by "Pending", only pending tasks are displayed (completed tasks are hidden).
* **test_index_can_search_tasks:** Ensures the search box accurately finds tasks based on specific keywords (e.g., "Laravel").
* **test_index_shows_empty_state_when_no_tasks:** Confirms that the "No tasks found" message is displayed when the database is empty.

**── Create Tests (Creating New Tasks) ──**
* **test_can_view_create_task_page:** Checks if the create task form page loads without errors.
* **test_can_create_task_with_minimum_data:** Verifies that submitting only the required fields (title, status, priority) successfully saves to the database and shows a success message.
* **test_can_create_task_with_all_data:** Ensures that filling out all fields (including description and deadline) saves correctly to the database.

**── Validation Tests (Blocking Invalid Data) ──**
* **test_create_validates_required_fields:** Ensures the system blocks empty form submissions and returns validation error messages.
* **test_create_validates_title_max_length:** Verifies that titles exceeding 255 characters are rejected with a warning.
* **test_create_validates_invalid_status:** Ensures that invalid statuses (e.g., 'archived') are rejected.
* **test_create_validates_invalid_priority:** Ensures that invalid priorities (e.g., 'urgent') are rejected.
* **test_create_validates_past_due_date:** Confirms that setting a due date in the past is rejected with an error.

**── Show Tests (Viewing Details) ──**
* **test_can_view_task_detail:** Verifies that visiting a specific task's detail page displays the correct task title and information.

**── Update Tests (Editing Tasks) ──**
* **test_can_view_edit_task_page:** Checks if the edit form loads correctly and displays the existing data.
* **test_can_update_task:** Ensures that submitting the edit form updates the record in the database accurately.
* **test_update_validates_invalid_data:** Verifies that submitting invalid data during an update is blocked by validation.

**── Delete Tests (Deleting Tasks) ──**
* **test_can_delete_task:** Verifies that clicking delete soft-deletes the task (hidden but not permanently removed from the database) and shows a success message.

**── Status Update Tests (Quick Status Update) ──**
* **test_can_update_task_status_via_web:** Ensures a task's status can be changed directly (e.g., from 'pending' to 'completed') without using the full edit form.
* **test_update_status_validates_invalid_status:** Confirms that providing an invalid status during a quick update fails validation and leaves the task's original status unchanged.

---

### 2. TaskModelTest.php (Unit Tests)
This file does not test routes or pages. Instead, it isolates and tests the internal database queries, methods, and logic of the `Task` Eloquent model to ensure they function flawlessly.

**── Scope Tests (Filtering Specific Data from the Database) ──**
* **test_pending_scope_returns_only_pending_tasks:** Ensures `Task::pending()` fetches only tasks with a pending status.
* **test_in_progress_scope_returns_only_in_progress_tasks:** Ensures `Task::inProgress()` fetches only in-progress tasks.
* **test_completed_scope_returns_only_completed_tasks:** Ensures `Task::completed()` fetches only completed tasks.
* **test_high_priority_scope:** Ensures `Task::highPriority()` fetches only high-priority tasks.
* **test_overdue_scope_excludes_completed_tasks:** Guarantees that completed tasks are excluded from the overdue list, even if their deadline has passed.
* **test_overdue_scope_excludes_future_tasks:** Ensures that tasks with future deadlines are not incorrectly flagged as overdue.

**── Filter Scope Tests (Custom Search & Filter Logic) ──**
* **test_filter_scope_with_status:** Verifies the model's `filter()` method correctly returns data when filtered by status.
* **test_filter_scope_with_priority:** Verifies the `filter()` method correctly returns data when filtered by priority.
* **test_filter_scope_with_search:** Ensures searching by a keyword in the title works correctly inside the model query.
* **test_filter_scope_searches_description:** Confirms that searching by a keyword also checks within the task's description field.
* **test_filter_scope_with_multiple_filters:** Verifies that applying multiple conditions simultaneously (e.g., Pending + High Priority) accurately returns only the data matching both criteria.

**── Accessor Tests (Formatting Data for Display) ──**
* **test_is_overdue_accessor_returns_true_for_past_due_pending_task:** Ensures `is_overdue` returns true for a pending task whose deadline has passed.
* **test_is_overdue_accessor_returns_false_for_future_due_task:** Ensures `is_overdue` returns false for tasks with future deadlines.
* **test_is_overdue_accessor_returns_false_for_completed_past_due_task:** Ensures `is_overdue` returns false for tasks that are completed, even if the deadline has passed (completed tasks are not overdue).
* **test_is_overdue_accessor_returns_false_for_task_with_no_due_date:** Ensures `is_overdue` returns false for tasks with no deadline set.
* **test_status_badge_color_accessor:** Verifies the correct badge color string is returned based on status (pending=yellow, in_progress=blue, completed=green).
* **test_priority_badge_color_accessor:** Verifies the correct badge color string is returned based on priority (high=red, medium=orange, low=green).

**── Cast & Soft Delete Tests (Data Types & Delete Configuration) ──**
* **test_due_date_is_cast_to_carbon_instance:** Ensures the `due_date` string from the database is automatically converted into a Laravel Carbon date object for easy date manipulation.
* **test_task_uses_soft_deletes:** Verifies that deleting a task soft-deletes it (hides it from normal queries but keeps it in the database) rather than permanently erasing it.
* **test_task_can_be_force_deleted:** Ensures that using the `forceDelete()` function permanently removes the task record from the database.
---

## 📄 License

This project is developed for the technical assessment at Qtec Solution Limited.


