# 🛠️ Mini Project Management Tool

A web-based mini project management system built using **PHP** and **MySQL**. This app allows a manager to create projects, assign tasks to employees, and track progress while employees can update task status and give feedback.

---


## 👥 Roles

- **Manager** can:
  - Add/edit/delete projects and tasks
  - Assign tasks to employees
  - View submitted feedback
  - Create team members (employees)

- **Employee** can:
  - View assigned tasks and update their status
  - Submit feedback on projects/tasks

---

## 📦 Database Setup

To set up the database for this project, use the following SQL script:

📄 [Download `db.sql`](db.sql)

This script includes:
- Creation of the database `project_manager`
- Tables:
  - `users`: Stores login credentials and user roles (manager/employee)
  - `projects`: Project metadata
  - `tasks`: Tasks under specific projects
  - `task_team`: Assignment of tasks to employees with status
  - `feedback`: Feedback comments from employees
- Pre-inserted admin account:
  - **Email:** `admin@example.com`
  - **Password:** `admin123` (hashed)

💡 You can run this `.sql` file directly in phpMyAdmin or MySQL Workbench to initialize your project database.

---

## 🗂️ Folder Structure

```
project_manager/
├── index.php
├── login.php
├── logout.php
├── css/
│   └── style.css
├── uploads/
├── manager/
│   ├── dashboard.php
│   ├── projects.php
│   ├── tasks.php
│   ├── team.php
│   ├── assign_task.php
│   └── feedback.php
├── employee/
│   ├── dashboard.php
│   └── feedback.php
└── project_manager_schema.sql
```

---

## 🧪 Default Login Credentials

```
Manager Login:
Email: admin@example.com
Password: admin123
```

Employees can log in with the credentials set by the manager during creation. If no password is set, it defaults to `123456`.

---

## 🧰 Tech Stack

- **Frontend**: HTML, CSS (Vanilla)
- **Backend**: PHP (Core PHP, no frameworks)
- **Database**: MySQL

---

## ✅ Features

- Manager-employee login system
- Task status update by employee
- Feedback mechanism
- Simple and responsive UI
- PDF/doc upload for projects

---

## 📃 License

Free for educational and personal use.
