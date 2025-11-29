# Todo Application

A modern, full-stack Todo application built with **Vue.js 3** and **Laravel 11**, featuring a dark-themed UI with comprehensive task management capabilities.

![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat-square&logo=vue.js)
![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat-square&logo=laravel)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=flat-square&logo=mysql)

## Features

### Core Features
- ✅ **CRUD Operations** - Create, Read, Update, and Delete todos
- ✅ **Mark Complete/Incomplete** - Toggle task completion status
- ✅ **Responsive UI** - Beautiful, mobile-first design
- ✅ **Form Validation** - Client and server-side validation
- ✅ **RESTful API** - Well-structured API endpoints
- ✅ **Database Migrations** - Proper schema management

### Bonus Features
- 🏷️ **Categories/Tags** - Organize tasks by category
- 📅 **Due Dates** - Set and track deadlines with overdue indicators
- 🔍 **Search & Filter** - Find tasks quickly with multiple filters
- 📊 **Statistics Dashboard** - Visual overview of task status
- 🎯 **Priority Levels** - High, Medium, Low priority assignments
- 🔐 **User Authentication** - Register/Login with Laravel Sanctum (API ready)
- 🧪 **Automated Tests** - Comprehensive PHPUnit test suite

## Tech Stack

### Frontend
- **Vue.js 3** - Composition API with `<script setup>`
- **Pinia** - State management
- **Vite** - Build tool and dev server
- **Axios** - HTTP client
- **VueUse** - Utility composables

### Backend
- **Laravel 11** - PHP framework
- **Laravel Sanctum** - API authentication
- **MySQL** - Database (production-ready)
- **PHPUnit** - Testing framework

---

## Deployment Guide

### Option A: Deploy to Railway (Backend) + Vercel (Frontend)

This is the recommended approach for easy deployment without local PHP installation.

#### Step 1: Deploy Backend to Railway

1. **Create a Railway Account**: Go to [railway.app](https://railway.app) and sign up

2. **Create New Project**:
   - Click "New Project" → "Deploy from GitHub repo"
   - Connect your GitHub account and select your repository
   - Choose the `backend` folder as the root directory

3. **Add MySQL Database**:
   - In your Railway project, click "New" → "Database" → "MySQL"
   - Railway will automatically provide `DATABASE_URL`

4. **Set Environment Variables** in Railway:
   ```
   APP_NAME=Taskflow
   APP_ENV=production
   APP_KEY=base64:YOUR_GENERATED_KEY
   APP_DEBUG=false
   APP_URL=https://your-app.railway.app
   
   DB_CONNECTION=mysql
   MYSQL_URL=${DATABASE_URL}
   
   SESSION_DRIVER=cookie
   CACHE_STORE=file
   QUEUE_CONNECTION=sync
   
   FRONTEND_URL=https://your-frontend.vercel.app
   ```

5. **Generate APP_KEY**: Run locally or use an online generator:
   ```bash
   php artisan key:generate --show
   ```

6. **Deploy**: Railway will auto-deploy when you push to GitHub

#### Step 2: Deploy Frontend to Vercel

1. **Create a Vercel Account**: Go to [vercel.com](https://vercel.com) and sign up

2. **Import Project**:
   - Click "New Project" → "Import Git Repository"
   - Select your repository
   - Set **Root Directory** to `frontend`

3. **Configure Build Settings**:
   - Framework Preset: `Vite`
   - Build Command: `npm run build`
   - Output Directory: `dist`

4. **Set Environment Variables**:
   ```
   VITE_API_URL=https://your-backend.railway.app/api
   ```

5. **Deploy**: Click "Deploy"

---

### Option B: Deploy to Render (Backend) + Vercel (Frontend)

#### Backend on Render

1. **Create Render Account**: Go to [render.com](https://render.com)

2. **Create Web Service**:
   - New → Web Service → Connect GitHub repo
   - Root Directory: `backend`
   - Runtime: `PHP`
   - Build Command: `composer install --no-dev --optimize-autoloader`
   - Start Command: `php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT`

3. **Add MySQL**: Create a MySQL database on Render or use [PlanetScale](https://planetscale.com)

4. **Environment Variables**: Same as Railway above

---

### Option C: Local Development with Docker (No Local PHP Needed)

```bash
# Clone the repository
git clone https://github.com/yourusername/todo-app.git
cd todo-app

# Start with Docker
docker-compose up -d

# Backend: http://localhost:8000
# Frontend: http://localhost:5173
```

#### docker-compose.yml (create in project root):
```yaml
version: '3.8'

services:
  backend:
    build:
      context: ./backend
      dockerfile: Dockerfile
    ports:
      - "8000:8000"
    environment:
      - DB_CONNECTION=mysql
      - DB_HOST=db
      - DB_PORT=3306
      - DB_DATABASE=todo_app
      - DB_USERNAME=root
      - DB_PASSWORD=secret
    depends_on:
      - db

  frontend:
    build:
      context: ./frontend
      dockerfile: Dockerfile
    ports:
      - "5173:5173"
    environment:
      - VITE_API_URL=http://localhost:8000/api

  db:
    image: mysql:8.0
    environment:
      - MYSQL_ROOT_PASSWORD=secret
      - MYSQL_DATABASE=todo_app
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:
```

---

## Local Development Setup (If You Have PHP)

### Prerequisites
- PHP >= 8.2 with extensions: pdo_mysql, mbstring, xml, curl
- Composer >= 2.0
- Node.js >= 18.x
- MySQL 8.x

### Backend Setup
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed  # Optional: seed sample data
php artisan serve
```

### Frontend Setup
```bash
cd frontend
npm install
npm run dev
```

---

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/todos` | List all todos (with filters) |
| POST | `/api/todos` | Create a new todo |
| GET | `/api/todos/{id}` | Get a single todo |
| PUT | `/api/todos/{id}` | Update a todo |
| PATCH | `/api/todos/{id}/toggle` | Toggle completion status |
| DELETE | `/api/todos/{id}` | Delete a todo |
| GET | `/api/todos/categories` | Get all categories |
| GET | `/api/todos/stats` | Get todo statistics |

### Query Parameters for Filtering
- `search` - Search in title and description
- `category` - Filter by category
- `completed` - Filter by completion status (true/false)
- `priority` - Filter by priority (low/medium/high)
- `sort_by` - Sort field (created_at/due_date/title/priority)
- `sort_order` - Sort direction (asc/desc)

---

## Running Tests

```bash
cd backend
php artisan test
```

### Test Coverage
- ✅ List todos
- ✅ Create todo with validation
- ✅ Update todo
- ✅ Delete todo
- ✅ Toggle completion
- ✅ Filter by search, category, status
- ✅ Get categories and stats

---

## Project Structure

```
todo-app/
├── backend/                    # Laravel API
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
│   ├── railway.toml            # Railway deployment config
│   └── nixpacks.toml           # Nixpacks build config
│
└── frontend/                   # Vue.js SPA
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
    ├── vercel.json             # Vercel deployment config
    └── vite.config.js
```

---

## Design Decisions

### Frontend Architecture
- **Composition API**: Used Vue 3's Composition API with `<script setup>` for cleaner, more maintainable code
- **Pinia Store**: Centralized state management with clear separation of state, getters, and actions
- **Component Design**: Small, focused components with clear responsibilities
- **CSS Variables**: Consistent theming with CSS custom properties

### Backend Architecture
- **RESTful API**: Following REST conventions for predictable endpoints
- **Query Scopes**: Model scopes for reusable filtering logic
- **Stateless Sessions**: Cookie-based sessions for API compatibility
- **CORS Configuration**: Supports Vercel/Netlify deployments

### Database Design
- **MySQL**: Production-ready database
- **Flexible Schema**: Nullable fields for optional data
- **Soft Relationships**: Optional user association for future authentication

---

## Trade-offs & Considerations

1. **MySQL over SQLite**: Chose MySQL for production compatibility with cloud providers like Railway and PlanetScale.

2. **Cookie Sessions**: Using cookie-based sessions instead of database sessions for simpler deployment and stateless API behavior.

3. **No Authentication by Default**: Todos are publicly accessible for demo purposes. Auth endpoints exist and can be enabled.

4. **Separate Deployments**: Frontend and backend are deployed separately for scalability and flexibility.

---

