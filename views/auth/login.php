<?php
// If already logged in, redirect to dashboard
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php?page=dashboard');
    exit;
}
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lost Item Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            min-height: 100vh;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="login-container">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h4><i class="fas fa-search"></i> Digital Tracking & Reporting</h4>
                <small>Lost Items & Incidents</small>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form action="index.php?page=login&action=login" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <div class="text-center mt-3">
                    <a href="index.php?page=register">Don't have an account? Register</a>
                </div>

                <div class="text-center mt-3">
                    <a class="btn btn-secondary w-100" href="index.php?page=welcome">Back Home</a>
                </div>
            </div>
            <div class="card-footer text-center text-muted">
                &copy; <?= date('Y') ?> NIT - Tanzania
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>