
<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

$message = "";
$messageType = "";

// Fetch students and staff for dropdown menus
$students = $pdo->query(
    "SELECT student_id, name, room_no FROM students ORDER BY name"
)->fetchAll();

$staffMembers = $pdo->query(
    "SELECT staff_id, staff_name, department FROM staff ORDER BY staff_name"
)->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_id = (int) ($_POST["student_id"] ?? 0);
    $staff_id = !empty($_POST["staff_id"]) ? (int) $_POST["staff_id"] : null;
    $category = trim($_POST["category"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $complaint_date = $_POST["complaint_date"] ?? "";

    if ($student_id <= 0 || $category === "" || $description === "" || $complaint_date === "") {
        $message = "Please fill in all required fields.";
        $messageType = "error";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO complaints
                (student_id, staff_id, category, description, complaint_date)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $student_id,
                $staff_id,
                $category,
                $description,
                $complaint_date
            ]);

            $message = "Complaint added successfully!";
            $messageType = "success";

        } catch (PDOException $e) {
            $message = "Unable to add complaint. Please check the selected student and staff.";
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Complaint | HCMS</title>
    <link rel="stylesheet" href="../assets/style.css">

    <style>
        .form-card {
            background: white;
            max-width: 750px;
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

        .form-card textarea {
            min-height: 120px;
            resize: vertical;
        }

        .message {
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
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
        <p>Complaint Management</p>
    </header>

    <nav class="navigation">
        <a href="../index.php">Dashboard</a>
        <a href="../students/index.php">Students</a>
        <a href="../staff/index.php">Staff</a>
        <a href="index.php">Complaints</a>
        <a href="../reports/index.php">Reports</a>
        <a href="../auth/logout.php">Logout</a>
    </nav>

    <main class="container">

        <div class="form-card">

            <h2>Register New Complaint</h2>
            <p>Enter the complaint details below.</p>

            <?php if ($message !== ""): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($students)): ?>

                <div class="message error">
                    Please add a student before registering a complaint.
                </div>

                <a href="../students/add.php" class="btn">Add Student</a>

            <?php else: ?>

                <form method="POST">

                    <label>Select Student:</label>
                    <select name="student_id" required>
                        <option value="">-- Select Student --</option>

                        <?php foreach ($students as $student): ?>
                            <option
                                value="<?php echo $student["student_id"]; ?>"
                                <?php echo (($_POST["student_id"] ?? "") == $student["student_id"]) ? "selected" : ""; ?>
                            >
                                <?php echo htmlspecialchars($student["name"]); ?>
                                (Room <?php echo htmlspecialchars($student["room_no"]); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Assign Staff (Optional):</label>
                    <select name="staff_id">
                        <option value="">-- Not Assigned --</option>

                        <?php foreach ($staffMembers as $member): ?>
                            <option
                                value="<?php echo $member["staff_id"]; ?>"
                                <?php echo (($_POST["staff_id"] ?? "") == $member["staff_id"]) ? "selected" : ""; ?>
                            >
                                <?php echo htmlspecialchars($member["staff_name"]); ?>
                                (<?php echo htmlspecialchars($member["department"]); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Complaint Category:</label>
                    <select name="category" required>
                        <option value="">-- Select Category --</option>

                        <?php
                        $categories = ["Electrical", "Plumbing", "Cleanliness", "Furniture", "Internet", "Other"];
                        foreach ($categories as $category):
                        ?>
                            <option
                                value="<?php echo $category; ?>"
                                <?php echo (($_POST["category"] ?? "") === $category) ? "selected" : ""; ?>
                            >
                                <?php echo $category; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Complaint Description:</label>
                    <textarea
                        name="description"
                        rows="5"
                        placeholder="Describe the complaint in detail..."
                        required
                    ><?php echo htmlspecialchars($_POST["description"] ?? ""); ?></textarea>

                    <label>Complaint Date:</label>
                    <input
                        type="date"
                        name="complaint_date"
                        value="<?php echo htmlspecialchars($_POST["complaint_date"] ?? date('Y-m-d')); ?>"
                        required
                    >

                    <div class="form-actions">
                        <button type="submit">Submit Complaint</button>
                        <a href="index.php">Back to Complaints</a>
                    </div>

                </form>

            <?php endif; ?>

        </div>

    </main>

</body>
</html>
