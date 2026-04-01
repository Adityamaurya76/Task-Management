# Task Management API

A RESTful API for task management built with Laravel. Users can register, authenticate, and manage their tasks with features like pagination, filtering, and task completion tracking.

## Features

- **User Authentication**: Register and login with JWT token-based authentication (Laravel Sanctum)
- **Task Management**: Create, read, update, and delete tasks
- **Task Status**: Mark tasks as pending or completed
- **Pagination**: View tasks with pagination support (current page, limit, total pages)
- **Filtering**: Filter tasks by status (pending/completed)
- **Validation**: Comprehensive input validation with due date constraints
- **Form Requests**: Organized validation logic using Laravel Form Requests

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL/MariaDB
- Node.js (for frontend dependencies, optional)

## Setup Instructions

### 1. Clone the Repository

```bash
git clone <your-repo-url>
cd task-api
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

```bash
cp .env.example .env
```

Edit `.env` file and configure:
- Database connection details (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
- APP_URL (default: http://localhost:8000)

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Run Migrations

```bash
php artisan migrate
```

This will create the necessary tables including:
- `users` - User accounts
- `tasks` - Task data with due dates
- `personal_access_tokens` - Authentication tokens

### 6. Start the Development Server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## API Endpoints

### Base URL
```
http://localhost:8000/api/v1
```

### Authentication Endpoints

#### Register
- **Endpoint**: `POST /auth/register`
- **Body**:
  ```json
  {
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }
  ```
- **Response**: User object with ID and email

#### Login
- **Endpoint**: `POST /auth/login`
- **Body**:
  ```json
  {
    "email": "john@example.com",
    "password": "password123"
  }
  ```
- **Response**: 
  ```json
  {
    "success": true,
    "access_token": "token_string",
    "token_type": "Bearer",
    "message": "Login successful"
  }
  ```

### Task Endpoints (Requires Authentication)

Add header: `Authorization: Bearer <access_token>`

#### List All Tasks
- **Endpoint**: `GET /auth/tasks`
- **Query Parameters**:
  - `status` (optional): Filter by 'pending' or 'completed'
- **Response**:
  ```json
  {
    "message": "List of tasks",
    "tasks": [...],
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total": 25,
      "total_pages": 3
    }
  }
  ```

#### Create Task
- **Endpoint**: `POST /auth/tasks`
- **Body**:
  ```json
  {
    "title": "Buy groceries",
    "description": "Milk, eggs, bread",
    "status": "pending",
    "due_date": "2026-04-15"
  }
  ```
- **Validation Rules**:
  - `title`: Required, string, max 255 characters
  - `description`: Optional, string
  - `status`: Required, must be 'pending' or 'completed'
  - `due_date`: Required, must be in format DD-MM-YYYY and not in the past

#### Update Task
- **Endpoint**: `PATCH /auth/tasks/update/{id}`
- **Body** (all fields optional):
  ```json
  {
    "title": "Buy groceries",
    "description": "Milk, eggs, bread",
    "status": "completed",
    "due_date": "2026-04-15"
  }
  ```
- **Note**: Send only the fields you want to update

#### Get Task Details
- **Endpoint**: `GET /auth/tasks/details/{id}`
- **Response**: Single task object

#### Delete Task
- **Endpoint**: `DELETE /auth/tasks/delete/{id}`
- **Response**: Success message

#### Mark Task as Completed
- **Endpoint**: `PATCH /auth/tasks/status/{id}`
- **Response**: Updated task object with status 'completed'

## Testing with Postman

1. Create or import the collection from `postman_collection.json` (if provided)
2. Set the `{{token}}` environment variable after login
3. Include the Bearer token in the Authorization header automatically
4. All endpoints support the standard HTTP methods (GET, POST, PATCH, DELETE, PUT)

## Error Handling

All error responses follow a consistent format:
```json
{
  "success": false,
  "message": "Error description",
  "errors": {} // validation errors if applicable
}
```

## Response Format
### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {...}
}
```

### Error Response (Validation)
```json
{
  "success": false,
  "errors": {
    "field_name": ["Error message"]
  },
  "message": "Validation failed"
}
```

### Error Response (Not Found)
```json
{
  "success": false,
  "message": "Task not found"
}
```

## File Structure

```
app/
├── Models/
│   ├── User.php          # User model with token support
│   └── Task.php          # Task model with relationships
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php    # Register and login
│   │   └── TaskController.php    # Task CRUD operations
│   └── Requests/
│       ├── RegisterRequest.php    # User registration validation
│       ├── LoginRequest.php       # User login validation
│       ├── StoreTaskRequest.php   # Task creation validation
│       └── UpdateTaskRequest.php  # Task update validation
database/
├── migrations/
│   └── *_create_tasks_table.php  # Task table with due_date
└── factories/
    └── UserFactory.php            # User factory for testing
routes/
├── api.php                        # API routes
tests/
├── Feature/                       # Feature tests
└── Unit/                          # Unit tests
```


