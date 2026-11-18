
<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

// Get filter values
$search = trim($_GET["search"] ?? "");
$category = $_GET["category"] ?? "";
$status = $_GET["status"] ?? "";
$date_from = $_GET["date_from"] ?? "";
$date_to = $_GET["date_to"] ?? "";

// Build SQL conditions
$conditions = [];
$params = [];

if ($search !== "") {
    $conditions[] = "(s.name LIKE ? OR s.room_no LIKE ? OR c.description LIKE ?)";
    $term = "%$search%";
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}

$validCategories = [
    "Electrical", "Plumbing", "Cleanliness",
    "Furniture", "Internet", "Other"
];

if (in_array($category, $validCategories, true)) {
    $conditions[] = "c.category = ?";
    $params[] = $category;
} else {
    $category = "";
}

$validStatuses = ["Pending", "In Progress", "Resolved"];

if (in_array($status, $validStatuses, true)) {
    $conditions[] = "c.status = ?";
    $params[] = $status;
} else {
    $status = "";
}

if ($date_from !== "" && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_from)) {
    $conditions[] = "c.complaint_date >= ?";
    $params[] = $date_from;
} else {
    $date_from = "";
}

if ($date_to !== "" && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_to)) {
    $conditions[] = "c.complaint_date <= ?";
    $params[] = $date_to;
} else {
    $date_to = "";
}

// Fetch complaints
$sql = "
    SELECT
        c.complaint_id,
        s.name AS student_name,
        s.room_no,
        st.staff_name,
        c.category,
        c.description,
        c.complaint_date,
        c.status
    FROM complaints c
    JOIN students s ON c.student_id = s.student_id
    LEFT JOIN staff st ON c.staff_id = st.staff_id
";

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY c.complaint_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$complaints = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Records | HCMS</title>
    <link rel="stylesheet" href="../assets/style.css">

    <style>
        .filter-box {
            background: white;
            padding: 22px;
            border-radius: 12px;
            border: 1px solid #e5eaf2;
            margin: 20px 0;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .filter-grid input,
        .filter-grid select {
            width: 100%;
        }

        .filter-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .reset-link {
            padding: 10px 15px;
            color: #475569;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .status {
            font-weight: bold;
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

        <h2>Complaint Records</h2>

        <p>
            <a href="add.php" class="btn">+ Register New Complaint</a>
        </p>

        <section class="filter-box">

            <h3>Search and Filter Complaints</h3>

            <form method="GET">

                <div class="filter-grid">

                    <div>
                        <label>Search</label>
                        <input type="text" name="search"
                               placeholder="Student name, room or description"
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>

                    <div>
                        <label>Category</label>
                        <select name="category">
                            <option value="">All Categories</option>

                            <?php foreach ($validCategories as $item): ?>
                                <option value="<?php echo $item; ?>"
                                    <?php echo $category === $item ? "selected" : ""; ?>>
                                    <?php echo $item; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label>Status</label>
                        <select name="status">
                            <option value="">All Statuses</option>

                            <?php foreach ($validStatuses as $item): ?>
                                <option value="<?php echo $item; ?>"
                                    <?php echo $status === $item ? "selected" : ""; ?>>
                                    <?php echo $item; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label>Date From</label>
                        <input type="date" name="date_from"
                               value="<?php echo htmlspecialchars($date_from); ?>">
                    </div>

                    <div>
                        <label>Date To</label>
                        <input type="date" name="date_to"
                               value="<?php echo htmlspecialchars($date_to); ?>">
                    </div>

                </div>

                <div class="filter-actions">
                    <button type="submit">Apply Filters</button>
                    <a href="index.php" class="reset-link">Reset Filters</a>
                </div>

            </form>

        </section>

        <h2>Complaint Results (<?php echo count($complaints); ?>)</h2>

        <div class="table-wrapper">

            <table>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Room</th>
                    <th>Assigned Staff</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

                <?php foreach ($complaints as $complaint): ?>
                <tr>
                    <td><?php echo $complaint["complaint_id"]; ?></td>

                    <td><?php echo htmlspecialchars($complaint["student_name"]); ?></td>

                    <td><?php echo htmlspecialchars($complaint["room_no"]); ?></td>

                    <td>
                        <?php echo htmlspecialchars($complaint["staff_name"] ?? "Not Assigned"); ?>
                    </td>

                    <td><?php echo htmlspecialchars($complaint["category"]); ?></td>

                    <td><?php echo htmlspecialchars($complaint["description"]); ?></td>

                    <td><?php echo htmlspecialchars($complaint["complaint_date"]); ?></td>

                    <td class="status">
                        <?php echo htmlspecialchars($complaint["status"]); ?>
                    </td>

                    <td>
                        <a href="edit.php?id=<?php echo $complaint['complaint_id']; ?>">
                            Edit
                        </a>

                        |

                        <form action="delete.php" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this complaint?');"
                              style="display:inline;">

                            <input type="hidden" name="id"
                                   value="<?php echo $complaint['complaint_id']; ?>">

                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if (empty($complaints)): ?>
                <tr>
                    <td colspan="9">No complaints found matching your filters.</td>
                </tr>
                <?php endif; ?>

            </table>

        </div>

        <p>
            <a href="../index.php">Back to Dashboard</a>
        </p>

    </main>

</body>
</html>
