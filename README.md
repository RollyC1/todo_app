# Taskflow Todo Application

A full-stack Todo application built with Vue.js 3 and Laravel 11.

## Live Demo

- **Frontend:** https://todo-app-rollyc1.vercel.app *(update with your actual URL)*
- **Backend API:** https://todo-backend-7bub.onrender.com/api

## Features

### Core Features
- CRUD Operations (Create, Read, Update, Delete)
- Mark todos as complete/incomplete
- Responsive UI design
- Client and server-side form validation
- RESTful API endpoints
- Database migrations

### Bonus Features
- Categories for organizing tasks
- Due dates with overdue indicators
- Search and filter functionality
- Statistics dashboard
- Priority levels (High, Medium, Low)
- User authentication endpoints (API ready)
- Automated tests (14 tests, 71 assertions)

## Tech Stack

### Frontend
- Vue.js 3 (Composition API)
- Pinia (State management)
- Vite (Build tool)
- Axios (HTTP client)

### Backend
- Laravel 11
- PostgreSQL
- PHPUnit (Testing)

### Deployment
- Frontend: Vercel
- Backend: Render
- Database: Render PostgreSQL

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/todos` | List all todos |
| POST | `/api/todos` | Create a new todo |
| GET | `/api/todos/{id}` | Get a single todo |
| PUT | `/api/todos/{id}` | Update a todo |
| PATCH | `/api/todos/{id}/toggle` | Toggle completion status |
| DELETE | `/api/todos/{id}` | Delete a todo |
| GET | `/api/todos/categories` | Get all categories |
| GET | `/api/todos/stats` | Get statistics |

### Query Parameters
- `search` - Search in title and description
- `category` - Filter by category
- `completed` - Filter by status (true/false)
- `priority` - Filter by priority (low/medium/high)
- `sort_by` - Sort field (created_at, due_date, title, priority)
- `sort_order` - Sort direction (asc/desc)

## Project Structure

```
todo-app/
├── backend/
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   │   ├── TodoController.php
│   │   │   └── AuthController.php
│   │   └── Models/
│   │       └── Todo.php
│   ├── database/
│   │   ├── factories/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/api.php
│   ├── tests/Feature/TodoTest.php
│   └── Dockerfile
│
└── frontend/
    ├── src/
    │   ├── components/
    │   │   ├── TodoItem.vue
    │   │   ├── TodoForm.vue
    │   │   ├── TodoFilters.vue
    │   │   └── StatsPanel.vue
    │   ├── services/api.js
    │   ├── stores/todos.js
    │   ├── App.vue
    │   └── main.js
    ├── vercel.json
    └── vite.config.js
```

## Deployment

### Backend (Render)

1. Create a PostgreSQL database on Render
2. Create a Web Service with Docker runtime
3. Set root directory to `backend`
4. Configure environment variables:
   ```
   APP_NAME=Taskflow
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:your-generated-key
   DB_CONNECTION=pgsql
   DATABASE_URL=your-external-database-url
   SESSION_DRIVER=cookie
   CACHE_STORE=file
   QUEUE_CONNECTION=sync
   ```

### Frontend (Vercel)

1. Import repository
2. Set root directory to `frontend`
3. Add environment variable:
   ```
   VITE_API_URL=https://your-backend.onrender.com/api
   ```

## Local Development

### Backend
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### Frontend
```bash
cd frontend
npm install
npm run dev
```

## Running Tests

```bash
cd backend
php artisan test
```

## Technical Decisions

**Frontend Architecture**
- Used Vue 3 Composition API for cleaner component logic
- Pinia for centralized state management
- CSS custom properties for consistent theming

**Backend Architecture**
- RESTful API design
- Model scopes for reusable query filters
- Docker containerization for deployment

**Database**
- PostgreSQL for production (Render free tier)
- Flexible schema with nullable fields for optional data

**Trade-offs**
- Public todos by default for demo purposes (auth can be enabled)
- External database URL required for Render free tier
- Separate frontend/backend deployments for flexibility
