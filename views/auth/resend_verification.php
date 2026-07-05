<?php
$pageTitle = 'Resend Verification';
$email = $_GET['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); min-height: 100vh; }
        .container { max-width: 450px; margin: 100px auto; }
        .card { border-radius: 15px; }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h5>Resend Verification Email</h5>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <p class="text-muted">Enter your email address to receive a new verification OTP.</p>
            <form method="POST" action="index.php?page=resend_verification&action=send" id="resendForm">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" id="resendBtn">
                    <span id="resendBtnText">Send OTP</span>
                    <span id="resendBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </form>
            <div class="mt-3 text-center">
                <a href="index.php?page=login">Back to Login</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('resendForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('resendBtn');
        const btnText = document.getElementById('resendBtnText');
        const spinner = document.getElementById('resendBtnSpinner');
        // Disable button and show spinner
        btn.disabled = true;
        btnText.textContent = 'Sending...';
        spinner.classList.remove('d-none');
        // Form will submit; no need to preventDefault
    });
</script>
</body>
</html>