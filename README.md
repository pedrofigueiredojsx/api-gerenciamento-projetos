# API Documentation

## 🔐 Authentication

### Register
```http
POST /api/register
```
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Login
```http
POST /api/login
```
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

### Get User Info
```http
GET /api/me
Authorization: Bearer {token}
```

### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

---

## 📁 Projects

### List Projects
```http
GET /api/projects
```

### Create Project
```http
POST /api/projects
```
```json
{
  "name": "New Project",
  "description": "Project description"
}
```

### Get Project
```http
GET /api/projects/{id}
```

### Update Project
```http
PUT /api/projects/{id}
```
```json
{
  "name": "Updated Project",
  "description": "Updated description"
}
```

### Delete Project
```http
DELETE /api/projects/{id}
```

### Project Stats
```http
GET /api/projects/{id}/stats
```

---

## ✅ Tasks

### List Project Tasks
```http
GET /api/projects/{project_id}/tasks
```

### Create Task
```http
POST /api/projects/{project_id}/tasks
```
```json
{
  "title": "Task title",
  "description": "Task description",
  "status": "pending"
}
```

### Get Task
```http
GET /api/tasks/{id}
```

### Update Task
```http
PUT /api/tasks/{id}
```
```json
{
  "title": "Updated task",
  "status": "completed"
}
```

### Delete Task
```http
DELETE /api/tasks/{id}
```

---

## 📊 Reports

### List Available Reports
```http
GET /api/projects/{project_id}/reports
```

### Project Report
```http
GET /api/projects/{project_id}/reports/project
```

### Tasks Report
```http
GET /api/projects/{project_id}/reports/tasks
```

### Team Report
```http
GET /api/projects/{project_id}/reports/team
```

### Custom Report
```http
POST /api/projects/{project_id}/reports/custom
```
```json
{
  "date_from": "2024-01-01",
  "date_to": "2024-12-31",
  "status": ["pending", "completed"],
  "fields": ["title", "status", "created_at"]
}
```

---

## 📝 Notes

- All endpoints except `/register` and `/login` require authentication
- Use `Authorization: Bearer {token}` header for authenticated requests
- Base URL: `/api`
