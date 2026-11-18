
<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

if (!isset($_GET["id"])) {
    die("Staff ID is missing.");
}

$id = (int) $_GET["id"];

$stmt = $pdo->prepare("SELECT * FROM staff WHERE staff_id = ?");
$stmt->execute([$id]);
$member = $stmt->fetch();

if (!$member) {
    die("Staff member not found.");
}

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $staff_name = trim($_POST["staff_name"] ?? "");
    $department = trim($_POST["department"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");

    if ($staff_name === "" || $department === "" || $mobile === "") {
        $message = "Please fill in all fields.";
        $messageType = "error";
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE staff
                SET staff_name = ?, department = ?, mobile = ?
                WHERE staff_id = ?
            ");

            $stmt->execute([$staff_name, $department, $mobile, $id]);

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $message = "Something went wrong. Please try again.";
            $messageType = "error";
        }
    }

    $member["staff_name"] = $staff_name;
    $member["department"] = $department;
    $member["mobile"] = $mobile;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff | HCMS</title>
    <link rel="stylesheet" href="../assets/style.css">

    <style>
        .form-card {
            background: white;
            max-width: 650px;
            margin: 25px auto;
            padding: 30px;
            border-radius: 12px;
            border: 1px solid #e5eaf2;
            box-shadow: 0 3px 12px rgba(0,0,0,.04);
        }

        .form-card label {
            display: block;
            margin-top: 12px;
        }

        .message {
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 15px;
        }
    </style>
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

        <div class="form-card">

            <h2>Edit Staff Details</h2>
            <p>Update the staff member's information below.</p>

            <?php if ($message !== ""): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <label>Staff Name:</label>
                <input
                    type="text"
                    name="staff_name"
                    value="<?php echo htmlspecialchars($member["staff_name"]); ?>"
                    required
                >

                <label>Department:</label>
                <input
                    type="text"
                    name="department"
                    value="<?php echo htmlspecialchars($member["department"]); ?>"
                    required
                >

                <label>Mobile Number:</label>
                <input
                    type="text"
                    name="mobile"
                    value="<?php echo htmlspecialchars($member["mobile"]); ?>"
                    required
                >

                <div class="form-actions">
                    <button type="submit">Update Staff</button>
                    <a href="index.php">Back to Staff Records</a>
                </div>

            </form>

        </div>

    </main>

</body>
</html>
