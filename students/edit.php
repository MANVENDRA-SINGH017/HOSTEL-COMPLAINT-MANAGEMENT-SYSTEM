
<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

if (!isset($_GET["id"])) {
    die("Student ID is missing.");
}

$id = (int) $_GET["id"];

$stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    die("Student not found.");
}

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $room_no = trim($_POST["room_no"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if ($name === "" || $room_no === "" || $mobile === "" || $email === "") {
        $message = "Please fill in all fields.";
        $messageType = "error";
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE students
                SET name = ?, room_no = ?, mobile = ?, email = ?
                WHERE student_id = ?
            ");

            $stmt->execute([$name, $room_no, $mobile, $email, $id]);

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "This email is already registered.";
            } else {
                $message = "Something went wrong. Please try again.";
            }
            $messageType = "error";
        }
    }

    $student["name"] = $name;
    $student["room_no"] = $room_no;
    $student["mobile"] = $mobile;
    $student["email"] = $email;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student | HCMS</title>
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

        <div class="form-card">

            <h2>Edit Student Details</h2>
            <p>Update the student's information below.</p>

            <?php if ($message !== ""): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <label>Student Name:</label>
                <input type="text" name="name"
                    value="<?php echo htmlspecialchars($student["name"]); ?>" required>

                <label>Room Number:</label>
                <input type="text" name="room_no"
                    value="<?php echo htmlspecialchars($student["room_no"]); ?>" required>

                <label>Mobile Number:</label>
                <input type="text" name="mobile"
                    value="<?php echo htmlspecialchars($student["mobile"]); ?>" required>

                <label>Email:</label>
                <input type="email" name="email"
                    value="<?php echo htmlspecialchars($student["email"]); ?>" required>

                <div class="form-actions">
                    <button type="submit">Update Student</button>
                    <a href="index.php">Back to Students</a>
                </div>

            </form>

        </div>

    </main>

</body>
</html>
