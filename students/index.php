
<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

$stmt = $pdo->query("SELECT * FROM students ORDER BY student_id DESC");
$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students | HCMS</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

    <header class="header">
        <h1>Hostel Complaint Management System</h1>
        <p>Student Management</p>
    </header>

    <nav class="navigation">
        <a href="../index.php">Dashboard</a>
        <a href="index.php">Students</a>
        <a href="../staff/index.php">Staff</a>
        <a href="../complaints/index.php">Complaints</a>
        <a href="../reports/index.php">Reports</a>
        <a href="../auth/logout.php">Logout</a>
    </nav>

    <main class="container">

        <h2>Student Records</h2>

        <p>
            <a href="add.php" class="btn">+ Add New Student</a>
        </p>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Room Number</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Action</th>
            </tr>

            <?php foreach ($students as $student): ?>
            <tr>
                <td><?php echo $student["student_id"]; ?></td>

                <td><?php echo htmlspecialchars($student["name"]); ?></td>

                <td><?php echo htmlspecialchars($student["room_no"]); ?></td>

                <td><?php echo htmlspecialchars($student["mobile"]); ?></td>

                <td><?php echo htmlspecialchars($student["email"]); ?></td>

                <td>
                    <a href="edit.php?id=<?php echo $student['student_id']; ?>">
                        Edit
                    </a>

                    |

                    <form action="delete.php" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this student?');"
                          style="display:inline;">

                        <input type="hidden" name="id"
                               value="<?php echo $student['student_id']; ?>">

                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if (empty($students)): ?>
            <tr>
                <td colspan="6">No student records found.</td>
            </tr>
            <?php endif; ?>

        </table>

        <p>
            <a href="../index.php">Back to Dashboard</a>
        </p>

    </main>

</body>
</html>
