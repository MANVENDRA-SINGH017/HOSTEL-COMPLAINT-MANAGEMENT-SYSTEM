
<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

$stmt = $pdo->query("SELECT * FROM staff ORDER BY staff_id DESC");
$staffMembers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff | HCMS</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

    <header class="header">
        <h1>Hostel Complaint Management System</h1>
        <p>Staff Management</p>
    </header>

    <nav class="navigation">
        <a href="../index.php">Dashboard</a>
        <a href="../students/index.php">Students</a>
        <a href="index.php">Staff</a>
        <a href="../complaints/index.php">Complaints</a>
        <a href="../reports/index.php">Reports</a>
        <a href="../auth/logout.php">Logout</a>
    </nav>

    <main class="container">

        <h2>Staff Records</h2>

        <p>
            <a href="add.php" class="btn">+ Add New Staff</a>
        </p>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Mobile</th>
                <th>Action</th>
            </tr>

            <?php foreach ($staffMembers as $member): ?>
            <tr>
                <td><?php echo $member["staff_id"]; ?></td>

                <td><?php echo htmlspecialchars($member["staff_name"]); ?></td>

                <td><?php echo htmlspecialchars($member["department"]); ?></td>

                <td><?php echo htmlspecialchars($member["mobile"]); ?></td>

                <td>
                    <a href="edit.php?id=<?php echo $member['staff_id']; ?>">
                        Edit
                    </a>

                    |

                    <form action="delete.php" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this staff member?');"
                          style="display:inline;">

                        <input type="hidden" name="id"
                               value="<?php echo $member['staff_id']; ?>">

                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if (empty($staffMembers)): ?>
            <tr>
                <td colspan="5">No staff records found.</td>
            </tr>
            <?php endif; ?>

        </table>

        <p>
            <a href="../index.php">Back to Dashboard</a>
        </p>

    </main>

</body>
</html>
