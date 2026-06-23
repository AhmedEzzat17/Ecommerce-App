# ToDo List API

A RESTful API built with Laravel 12 and MySQL.

---

## Setup

```bash
composer install
php artisan key:generate
```

```bash
php artisan migrate
php artisan serve
```

---

## API Endpoints

> Base URL: `http://127.0.0.1:8000/api`
> 
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/todos` | List all todos |
| POST | `/todos` | Create a todo |
| GET | `/todos/{id}` | View a todo |
| PUT | `/todos/{id}` | Update a todo |
| DELETE | `/todos/{id}` | Delete a todo |

**Create/Update Body:**
```json
{
    "title": "My Task",
    "description": "optional",
    "status": "pending",
    "due_date": "2025-12-31"
}
```

---

## Auth (Phase 2)

**Register:** `POST /api/register`
```json
{ "name": "Ahmed", "email": "ahmed@example.com", "password": "123456" }
```

**Login:** `POST /api/login`
```json
{ "email": "ahmed@example.com", "password": "123456" }
```

After login, add to every request header:
```
Authorization: Bearer YOUR_TOKEN_HERE
```
Ahmed Ezzat 
