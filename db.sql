
-- Create Database
CREATE DATABASE project_manager;
USE project_manager;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('manager', 'employee') NOT NULL,
    emp_id VARCHAR(20) DEFAULT NULL
);

-- Create projects table
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    document VARCHAR(255),
    created_by INT
);

-- Create tasks table
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    deadline DATE,
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- Create task_team table
CREATE TABLE task_team (
    task_id INT,
    employee_id INT,
    status VARCHAR(20) DEFAULT 'pending',
    PRIMARY KEY (task_id, employee_id),
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create feedback table
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT,
    user_id INT,
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert admin user (password: admin123)
INSERT INTO users (name, email, password, role) 
VALUES (
    'Admin',
    'admin@example.com',
    '$2y$10$gZI.TJjwecVLQyBduGg4W.uYWd2VlIs/tSPAzRelrgidHPKnH70HC',
    'manager'
);
