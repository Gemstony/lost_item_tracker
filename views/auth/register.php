<?php
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Lost Item Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .btn-success {
            background-color: #198754;
            border: none;
        }
        .btn-success:hover {
            background-color: #157347;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Create New Account</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['errors'])): ?>
                        <?php foreach ($_SESSION['errors'] as $err): ?>
                            <?= showAlert($err, 'danger'); ?>
                        <?php endforeach; unset($_SESSION['errors']); ?>
                    <?php endif; ?>
                    <form method="POST" action="index.php?page=register&action=register" class="needs-validation" novalidate id="registerForm">
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" value="<?= htmlspecialchars($old['fullname'] ?? '') ?>" minlength="2" maxlength="100" autocomplete="name" required>
                            <div class="invalid-feedback">Enter your full name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" maxlength="150" autocomplete="email" required>
                            <div class="invalid-feedback">Enter a valid email address.</div>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone (optional)</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" inputmode="tel" autocomplete="tel" maxlength="20" pattern="^\+?[0-9\s().-]{7,20}$" title="Use 7 to 15 digits, optionally starting with +. Spaces, dashes, and parentheses are allowed.">
                            <div class="invalid-feedback">Enter a valid phone number, for example +255712345678.</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password (min 6 characters)</label>
                            <input type="password" class="form-control" id="password" name="password" minlength="6" autocomplete="new-password" required>
                            <div class="invalid-feedback">Password must be at least 6 characters.</div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6" autocomplete="new-password" required>
                            <div class="invalid-feedback">Confirm your password.</div>
                        </div>
                        <button type="submit" class="btn btn-success w-100" id="registerBtn">
                            <span id="registerBtnText">Register</span>
                            <span id="registerBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </form>
                    <div class="mt-3 text-center">
                        <a href="index.php?page=login">Already have an account? Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (() => {
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach((form) => {
            const password = form.querySelector('input[name="password"]');
            const confirmPassword = form.querySelector('input[name="confirm_password"]');

            const validatePasswordMatch = () => {
                if (!password || !confirmPassword) return;
                confirmPassword.setCustomValidity(
                    confirmPassword.value && password.value !== confirmPassword.value
                        ? 'Passwords do not match.'
                        : ''
                );
            };

            password.addEventListener('input', validatePasswordMatch);
            confirmPassword.addEventListener('input', validatePasswordMatch);

            form.addEventListener('submit', function(event) {
                validatePasswordMatch();
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });

        // Handle button loading state
        const registerForm = document.getElementById('registerForm');
        const registerBtn = document.getElementById('registerBtn');
        const btnText = document.getElementById('registerBtnText');
        const btnSpinner = document.getElementById('registerBtnSpinner');

        registerForm.addEventListener('submit', function(e) {
            if (!registerForm.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                return;
            }
            // Disable button and show spinner
            registerBtn.disabled = true;
            btnText.textContent = 'Processing...';
            btnSpinner.classList.remove('d-none');
            // The form will submit normally; no need to preventDefault
        });
    })();
</script>
</body>
</html>