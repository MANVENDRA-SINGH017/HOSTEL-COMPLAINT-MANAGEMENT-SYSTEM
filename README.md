
# Hostel Complaint Management System (HCMS)

A web-based Hostel Complaint Management System developed as a BCA 5th Semester mini project. The system helps hostel administrators manage student complaints, assign staff members, monitor complaint status, and generate reports.

## Project Overview

Managing hostel complaints manually can be time-consuming and difficult to track. This system provides a centralized platform where administrators can manage students, hostel staff, and complaints efficiently.

The application includes an admin dashboard, complaint management, search and filtering, and visual reports.

## Features

### 1. Admin Authentication
- Secure admin login and logout.
- Session-based authentication.
- Restricted access to management pages.

### 2. Dashboard
- Total registered students.
- Total staff members.
- Total complaints.
- Pending, In Progress, and Resolved complaint counts.
- Recent complaint records.

### 3. Student Management
- Add new students.
- View student records.
- Edit student details.
- Delete student records.
- Prevent duplicate email addresses.

### 4. Staff Management
- Add hostel staff members.
- View staff records.
- Update staff information.
- Delete staff records.

### 5. Complaint Management
- Register new complaints.
- Select the student submitting the complaint.
- Assign staff members.
- Select complaint categories.
- Update complaint status.
- Edit and delete complaint records.

### 6. Search and Filtering
- Search complaints by student name, room number, or description.
- Filter by category.
- Filter by complaint status.
- Filter by date range.

### 7. Reports and Analytics
- View overall complaint statistics.
- View complaints by category.
- Doughnut chart showing complaint status.
- Bar chart showing complaints by category.
- Line chart showing monthly complaint trends.
- Print complaint reports.

## Technologies Used

| Technology | Purpose |
|---|---|
| HTML5 | Webpage structure |
| CSS3 | Styling and layout |
| JavaScript | Client-side functionality |
| PHP | Backend development |
| MySQL / MariaDB | Database management |
| XAMPP | Local development server |
| phpMyAdmin | Database administration |
| Chart.js | Data visualization |

## Project Structure

```text
hostel-management/
│
├── assets/
│   └── style.css
│
├── auth/
│   ├── login.php
│   └── logout.php
│
├── config/
│   └── db.php
│
├── includes/
│   └── auth_check.php
│
├── students/
│   ├── add.php
│   ├── index.php
│   ├── edit.php
│   └── delete.php
│
├── staff/
│   ├── add.php
│   ├── index.php
│   ├── edit.php
│   └── delete.php
│
├── complaints/
│   ├── add.php
│   ├── index.php
│   ├── edit.php
│   └── delete.php
│
├── reports/
│   └── index.php
│
├── index.php
└── README.md
```

## Database Structure

Database Name: `hostel_management`

The system uses four main database tables:

### 1. admins
Stores administrator login credentials.

- admin_id
- username
- password
- created_at

### 2. students
Stores hostel student information.

- student_id
- name
- room_no
- mobile
- email
- created_at

### 3. staff
Stores hostel staff information.

- staff_id
- staff_name
- department
- mobile
- created_at

### 4. complaints
Stores student complaint records.

- complaint_id
- student_id
- staff_id
- category
- description
- complaint_date
- status
- created_at

The complaints table is connected to the students and staff tables using foreign keys.

## Installation and Setup

Follow these steps to run the project locally.

### Step 1: Install XAMPP

Download and install XAMPP.

Start Apache and MySQL from the XAMPP Control Panel.

### Step 2: Copy the Project

Copy the project folder into:

```text
C:\xampp\htdocs\
```

The project path should be:

```text
C:\xampp\htdocs\hostel-management\
```

### Step 3: Create the Database

1. Open phpMyAdmin.
2. Create a database named `hostel_management`.
3. Import the provided SQL database file if available.

### Step 4: Configure Database Connection

Open:

```text
config/db.php
```

Configure your database credentials.

Example:

```php
<?php
$host = "127.0.0.1";
$port = "3307";
$dbname = "hostel_management";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
```

Change the port and credentials according to your MySQL configuration.

### Step 5: Run the Project

Open your browser and visit:

```text
http://localhost:8080/hostel-management/
```

The default URL assumes Apache is running on port 8080.

### Step 6: Admin Login

Log in using the administrator account created during database setup.

Admin credentials should be configured separately and should not be shared publicly.

## Complaint Categories

The system supports the following complaint categories:

- Electrical
- Plumbing
- Cleanliness
- Furniture
- Internet
- Other

## Complaint Status

Each complaint can have one of three statuses:

- **Pending:** Complaint has been registered but not yet addressed.
- **In Progress:** Complaint is currently being handled.
- **Resolved:** Complaint has been successfully addressed.

## Security Features

- Password hashing using PHP password hashing functions.
- Password verification during login.
- Session-based authentication.
- Prepared SQL statements using PDO.
- HTML output escaping using `htmlspecialchars()`.
- Authentication checks on protected pages.

## Future Enhancements

- Student login and complaint submission portal.
- Email notifications for complaint updates.
- Admin remarks and resolution details.
- Complaint priority levels.
- Improved analytics and downloadable reports.
- Mobile-friendly interface improvements.

## Project Purpose

This project was developed as part of the BCA 5th Semester academic mini project to demonstrate practical implementation of database management, CRUD operations, PHP backend development, authentication, and reporting in a web-based application.

## Author

**BCA 5th Semester Mini Project**

Project: Hostel Complaint Management System (HCMS)
