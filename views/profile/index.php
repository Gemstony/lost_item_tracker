<?php
$pageTitle = 'My Profile';
require_once __DIR__ . '/../layouts/header.php';

$userId = $_SESSION['user_id'];

// Fetch current user data
$stmt = $pdo->prepare("SELECT fullname, email, phone FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-user-edit"></i> Edit Profile</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['profile_success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['profile_success']; unset($_SESSION['profile_success']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['profile_error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['profile_error']; unset($_SESSION['profile_error']); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>index.php?page=profile&action=update">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header bg-warning text-dark">
                    <h5><i class="fas fa-key"></i> Change Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>index.php?page=profile&action=changepassword">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password (min 6 characters)</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>