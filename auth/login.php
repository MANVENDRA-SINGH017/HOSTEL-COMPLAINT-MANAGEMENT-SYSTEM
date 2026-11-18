
<?php
session_start();
require_once "../config/db.php";

// Redirect to dashboard if already logged in
if (isset($_SESSION["admin_id"])) {
    header("Location: ../index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $pdo->prepare(
        "SELECT * FROM admins WHERE username = ?"
    );
    $stmt->execute([$username]);

    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin["password"])) {
        session_regenerate_id(true);

        $_SESSION["admin_id"] = $admin["admin_id"];
        $_SESSION["admin_username"] = $admin["username"];

        header("Location: ../index.php");
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | HCMS</title>

    <link rel="stylesheet" href="../assets/style.css">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-card {
            background: white;
            width: 100%;
            max-width: 420px;
            padding: 35px;
            border-radius: 12px;
            border: 1px solid #e5eaf2;
            box-shadow: 0 5px 20px rgba(0,0,0,.07);
        }

        .login-card h2 {
            margin-top: 0;
            text-align: center;
        }

        .login-card .subtitle {
            text-align: center;
            color: #64748b;
            margin-bottom: 25px;
        }

        .login-card label {
            display: block;
            margin-top: 15px;
        }

        .login-card button {
            width: 100%;
            margin-top: 15px;
        }

        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 15px;
            text-align: center;
        }

        .login-footer {
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            margin-top: 25px;
        }
    </style>
</head>

<body>

    <div class="login-card">

        <h2>HCMS</h2>
        <p class="subtitle">Hostel Complaint Management System</p>

        <h3 style="text-align:center;">Admin Login</h3>

        <?php if ($error): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label>Username</label>
            <input
                type="text"
                name="username"
                placeholder="Enter your username"
                value="<?php echo htmlspecialchars($_POST["username"] ?? ""); ?>"
                required
                autofocus
            >

            <label>Password</label>
            <input
                type="password"
                name="password"
                placeholder="Enter your password"
                required
            >

            <button type="submit">Login</button>

        </form>

        <p class="login-footer">
            Hostel Complaint Management System
        </p>

    </div>

</body>
</html>
