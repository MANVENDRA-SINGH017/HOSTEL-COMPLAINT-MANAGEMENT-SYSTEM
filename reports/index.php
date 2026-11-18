
<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

// Overall complaint statistics
$totalComplaints = $pdo->query(
    "SELECT COUNT(*) FROM complaints"
)->fetchColumn();

$pendingComplaints = $pdo->query(
    "SELECT COUNT(*) FROM complaints WHERE status = 'Pending'"
)->fetchColumn();

$inProgressComplaints = $pdo->query(
    "SELECT COUNT(*) FROM complaints WHERE status = 'In Progress'"
)->fetchColumn();

$resolvedComplaints = $pdo->query(
    "SELECT COUNT(*) FROM complaints WHERE status = 'Resolved'"
)->fetchColumn();

// Complaints grouped by category
$categoryReport = $pdo->query("
    SELECT category, COUNT(*) AS total
    FROM complaints
    GROUP BY category
    ORDER BY total DESC
")->fetchAll();

// Complaints grouped by month
$monthlyReport = $pdo->query("
    SELECT
        DATE_FORMAT(complaint_date, '%Y-%m') AS month,
        COUNT(*) AS total
    FROM complaints
    GROUP BY DATE_FORMAT(complaint_date, '%Y-%m')
    ORDER BY month ASC
")->fetchAll();

// All complaint records
$recentComplaints = $pdo->query("
    SELECT
        c.complaint_id,
        s.name,
        c.category,
        c.complaint_date,
        c.status
    FROM complaints c
    JOIN students s ON c.student_id = s.student_id
    ORDER BY c.complaint_date DESC
")->fetchAll();

// Prepare chart data
$statusLabels = ["Pending", "In Progress", "Resolved"];
$statusData = [
    (int)$pendingComplaints,
    (int)$inProgressComplaints,
    (int)$resolvedComplaints
];

$categoryLabels = [];
$categoryData = [];

foreach ($categoryReport as $category) {
    $categoryLabels[] = $category["category"];
    $categoryData[] = (int)$category["total"];
}

$monthLabels = [];
$monthData = [];

foreach ($monthlyReport as $month) {
    $monthLabels[] = date("M Y", strtotime($month["month"] . "-01"));
    $monthData[] = (int)$month["total"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Reports | HCMS</title>

    <link rel="stylesheet" href="../assets/style.css">

    <!-- Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }

        .report-card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            border: 1px solid #e5eaf2;
            box-shadow: 0 3px 12px rgba(0,0,0,.04);
        }

        .report-card h3 {
            font-size: 15px;
            color: #64748b;
            margin-bottom: 10px;
        }

        .report-card p {
            font-size: 28px;
            font-weight: bold;
            color: #315efb;
        }

        .report-section {
            background: white;
            padding: 22px;
            border-radius: 12px;
            border: 1px solid #e5eaf2;
            margin: 25px 0;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }

        .chart-card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            border: 1px solid #e5eaf2;
            box-shadow: 0 3px 12px rgba(0,0,0,.04);
            min-width: 0;
        }

        .chart-card h3 {
            color: #334155;
            font-size: 17px;
            margin-bottom: 20px;
        }

        .chart-container {
            position: relative;
            height: 280px;
            width: 100%;
        }

        .trend-chart {
            margin-top: 20px;
        }

        .print-button {
            margin-bottom: 15px;
        }

        @media (max-width: 600px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }
        }

        @media print {
            .header, .navigation, .print-button, .no-print {
                display: none !important;
            }

            .container {
                width: 100%;
                margin: 0;
            }

            .report-section, .report-card, .chart-card {
                box-shadow: none;
                border: 1px solid #ddd;
                break-inside: avoid;
            }

            .chart-container {
                height: 250px;
            }
        }
    </style>
</head>

<body>

    <header class="header">
        <h1>Hostel Complaint Management System</h1>
        <p>Complaint Reports</p>
    </header>

    <nav class="navigation">
        <a href="../index.php">Dashboard</a>
        <a href="../students/index.php">Students</a>
        <a href="../staff/index.php">Staff</a>
        <a href="../complaints/index.php">Complaints</a>
        <a href="index.php">Reports</a>
        <a href="../auth/logout.php">Logout</a>
    </nav>

    <main class="container">

        <h2>Overall Complaint Summary</h2>

        <div class="report-grid">

            <div class="report-card">
                <h3>Total Complaints</h3>
                <p><?php echo $totalComplaints; ?></p>
            </div>

            <div class="report-card">
                <h3>Pending</h3>
                <p><?php echo $pendingComplaints; ?></p>
            </div>

            <div class="report-card">
                <h3>In Progress</h3>
                <p><?php echo $inProgressComplaints; ?></p>
            </div>

            <div class="report-card">
                <h3>Resolved</h3>
                <p><?php echo $resolvedComplaints; ?></p>
            </div>

        </div>

        <!-- Complaint Charts -->
        <h2>Visual Complaint Analytics</h2>

        <div class="chart-grid">

            <div class="chart-card">
                <h3>Complaints by Status</h3>
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h3>Complaints by Category</h3>
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>

        </div>

        <div class="chart-card trend-chart">
            <h3>Monthly Complaint Trends</h3>
            <div class="chart-container">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Category Report Table -->
        <section class="report-section">
            <h2>Complaints by Category</h2>

            <table>
                <tr>
                    <th>Category</th>
                    <th>Total Complaints</th>
                </tr>

                <?php foreach ($categoryReport as $category): ?>
                <tr>
                    <td><?php echo htmlspecialchars($category["category"]); ?></td>
                    <td><?php echo $category["total"]; ?></td>
                </tr>
                <?php endforeach; ?>

                <?php if (empty($categoryReport)): ?>
                <tr>
                    <td colspan="2">No complaint data available.</td>
                </tr>
                <?php endif; ?>
            </table>
        </section>

        <!-- All Complaint Records -->
        <section class="report-section">
            <h2>All Complaint Records</h2>

            <button class="print-button" onclick="window.print()">
                Print Report
            </button>

            <table>
                <tr>
                    <th>Complaint ID</th>
                    <th>Student Name</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>

                <?php foreach ($recentComplaints as $complaint): ?>
                <tr>
                    <td><?php echo $complaint["complaint_id"]; ?></td>
                    <td><?php echo htmlspecialchars($complaint["name"]); ?></td>
                    <td><?php echo htmlspecialchars($complaint["category"]); ?></td>
                    <td><?php echo htmlspecialchars($complaint["complaint_date"]); ?></td>
                    <td><?php echo htmlspecialchars($complaint["status"]); ?></td>
                </tr>
                <?php endforeach; ?>

                <?php if (empty($recentComplaints)): ?>
                <tr>
                    <td colspan="5">No complaints recorded yet.</td>
                </tr>
                <?php endif; ?>
            </table>
        </section>

    </main>

    <!-- Chart.js Configuration -->
    <script>
        const statusLabels = <?php echo json_encode($statusLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        const statusData = <?php echo json_encode($statusData); ?>;

        const categoryLabels = <?php echo json_encode($categoryLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        const categoryData = <?php echo json_encode($categoryData); ?>;

        const monthLabels = <?php echo json_encode($monthLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        const monthData = <?php echo json_encode($monthData); ?>;

        // 1. Doughnut Chart - Complaints by Status
        new Chart(document.getElementById("statusChart"), {
            type: "doughnut",
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: [
                        "#f59e0b",
                        "#3b82f6",
                        "#22c55e"
                    ],
                    borderWidth: 2,
                    borderColor: "#ffffff"
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom"
                    }
                }
            }
        });

        // 2. Bar Chart - Complaints by Category
        new Chart(document.getElementById("categoryChart"), {
            type: "bar",
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: "Number of Complaints",
                    data: categoryData,
                    backgroundColor: "#6366f1",
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // 3. Line Chart - Monthly Complaint Trends
        new Chart(document.getElementById("monthlyChart"), {
            type: "line",
            data: {
                labels: monthLabels,
                datasets: [{
                    label: "Complaints Received",
                    data: monthData,
                    borderColor: "#315efb",
                    backgroundColor: "rgba(49, 94, 251, 0.15)",
                    fill: true,
                    tension: 0.35,
                    pointRadius: 5,
                    pointBackgroundColor: "#315efb"
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>
