<?php
$pageTitle = 'Verify Email';
$email = $_GET['email'] ?? $_SESSION['verify_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); min-height: 100vh; }
        .container { max-width: 450px; margin: 100px auto; }
        .card { border-radius: 15px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h5>Email Verification</h5>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <p class="text-muted">We sent a 6-digit OTP to <strong><?= htmlspecialchars($email) ?></strong>. Please enter it below.</p>
            <form method="POST" action="index.php?page=verify&action=verify">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <div class="mb-3">
                    <label class="form-label">OTP Code</label>
                    <input type="text" name="otp" class="form-control" placeholder="Enter 6-digit OTP" required maxlength="6" pattern="[0-9]{6}">
                </div>
                <button type="submit" class="btn btn-primary w-100">Verify</button>
            </form>
            <div class="mt-3 text-center">
                <form method="POST" action="index.php?page=verify&action=resend" style="display:inline">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                    <button type="submit" class="btn btn-link">Resend OTP</button>
                </form>
                <span class="mx-2">|</span>
                <a href="index.php?page=login">Back to Login</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>