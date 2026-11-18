
<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

if (!isset($_GET["id"])) {
    die("Complaint ID is missing.");
}

$id = (int) $_GET["id"];

$stmt = $pdo->prepare("SELECT * FROM complaints WHERE complaint_id = ?");
$stmt->execute([$id]);
$complaint = $stmt->fetch();

if (!$complaint) {
    die("Complaint not found.");
}

$students = $pdo->query(
    "SELECT student_id, name, room_no FROM students ORDER BY name"
)->fetchAll();

$staffMembers = $pdo->query(
    "SELECT staff_id, staff_name, department FROM staff ORDER BY staff_name"
)->fetchAll();

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_id = (int) ($_POST["student_id"] ?? 0);
    $staff_id = !empty($_POST["staff_id"]) ? (int) $_POST["staff_id"] : null;
    $category = trim($_POST["category"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $complaint_date = $_POST["complaint_date"] ?? "";
    $status = $_POST["status"] ?? "";

    $validStatuses = ["Pending", "In Progress", "Resolved"];
    $validCategories = ["Electrical", "Plumbing", "Cleanliness", "Furniture", "Internet", "Other"];

    if (
        $student_id <= 0 ||
        $category === "" ||
        $description === "" ||
        $complaint_date === "" ||
        !in_array($status, $validStatuses, true) ||
        !in_array($category, $validCategories, true)
    ) {
        $message = "Please enter valid information in all required fields.";
        $messageType = "error";
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE complaints
                SET student_id = ?,
                    staff_id = ?,
                    category = ?,
                    description = ?,
                    complaint_date = ?,
                    status = ?
                WHERE complaint_id = ?
            ");

            $stmt->execute([
                $student_id,
                $staff_id,
                $category,
                $description,
                $complaint_date,
                $status,
                $id
            ]);

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $message = "Unable to update complaint. Please check your entries.";
            $messageType = "error";
        }
    }

    $complaint["student_id"] = $student_id;
    $complaint["staff_id"] = $staff_id;
    $complaint["category"] = $category;
    $complaint["description"] = $description;
    $complaint["complaint_date"] = $complaint_date;
    $complaint["status"] = $status;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Complaint | HCMS</title>
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

            <h2>Edit Complaint</h2>
            <p>Update the complaint information below.</p>

            <?php if ($message !== ""): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <label>Student:</label>
                <select name="student_id" required>
                    <option value="">-- Select Student --</option>

                    <?php foreach ($students as $student): ?>
                        <option
                            value="<?php echo $student["student_id"]; ?>"
                            <?php echo $complaint["student_id"] == $student["student_id"] ? "selected" : ""; ?>
                        >
                            <?php echo htmlspecialchars($student["name"]); ?>
                            (Room <?php echo htmlspecialchars($student["room_no"]); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Assigned Staff:</label>
                <select name="staff_id">
                    <option value="">-- Not Assigned --</option>

                    <?php foreach ($staffMembers as $member): ?>
                        <option
                            value="<?php echo $member["staff_id"]; ?>"
                            <?php echo $complaint["staff_id"] == $member["staff_id"] ? "selected" : ""; ?>
                        >
                            <?php echo htmlspecialchars($member["staff_name"]); ?>
                            (<?php echo htmlspecialchars($member["department"]); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Category:</label>
                <select name="category" required>
                    <?php foreach (["Electrical", "Plumbing", "Cleanliness", "Furniture", "Internet", "Other"] as $category): ?>
                        <option
                            value="<?php echo $category; ?>"
                            <?php echo $complaint["category"] === $category ? "selected" : ""; ?>
                        >
                            <?php echo $category; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Description:</label>
                <textarea name="description" rows="5" required><?php echo htmlspecialchars($complaint["description"]); ?></textarea>

                <label>Complaint Date:</label>
                <input
                    type="date"
                    name="complaint_date"
                    value="<?php echo htmlspecialchars($complaint["complaint_date"]); ?>"
                    required
                >

                <label>Status:</label>
                <select name="status" required>
                    <?php foreach (["Pending", "In Progress", "Resolved"] as $status): ?>
                        <option
                            value="<?php echo $status; ?>"
                            <?php echo $complaint["status"] === $status ? "selected" : ""; ?>
                        >
                            <?php echo $status; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="form-actions">
                    <button type="submit">Update Complaint</button>
                    <a href="index.php">Back to Complaints</a>
                </div>

            </form>

        </div>

    </main>

</body>
</html>
