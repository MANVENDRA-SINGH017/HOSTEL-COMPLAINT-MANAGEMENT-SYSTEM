
<?php
require_once "../config/db.php";

$message = "";

$count = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();

if ($count > 0) {
    die("An admin account already exists. Setup is disabled.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || strlen($password) < 8) {
        $message = "Enter a username and a password of at least 8 characters.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            "INSERT INTO admins (username, password) VALUES (?, ?)"
        );

        $stmt->execute([$username, $hashedPassword]);

        $message = "Admin account created successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Admin</title>
</head>
<body>

<h2>Create Admin Account</h2>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST">
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password (minimum 8 characters):</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Create Admin</button>
</form>

</body>
</html>
