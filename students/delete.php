<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = (int) ($_POST["id"] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare(
        "DELETE FROM students WHERE student_id = ?"
    );
    $stmt->execute([$id]);
}

header("Location: index.php");
exit;
?>