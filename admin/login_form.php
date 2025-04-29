<?php
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['username'])) {
    header("Location: index.php?page=home");
    exit;
}

// Initialize error message
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include DB connection
    require_once 'db_connect.php'; // Make sure this sets $pdo properly

    // Get credentials
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user from DB
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['username'] = $user['username'];
        $_SESSION['login_id'] = $user['id'] ?? null;
        $_SESSION['login_type'] = $user['type'] ?? 'user';
        $_SESSION['user_name'] = $user['name'] ?? $user['username'];

        // Redirect to dashboard
        header("Location: index.php?page=home");
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CCTV Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center" style="height: 100vh;">
            <div class="col-md-4">
                <div class="card shadow-lg p-4">
                    <h2 class="text-center mb-4">Login</h2>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="login_form.php">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>