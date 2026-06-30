<?php
$pageTitle = 'Manage Users';
require_once __DIR__ . '/../layouts/header.php';

// Handle POST actions (add, edit, delete, role update) – these will be processed in index.php
// For now, just display users and provide forms.

// Fetch all users
$stmt = $pdo->query("SELECT id, fullname, email, phone, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5><i class="fas fa-users"></i> All Users</h5>
            <button type="button" class="btn btn-light btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus"></i> Add New User
            </button>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-bordered" id="usersTable">
                    <thead>
                        <tr><th>ID</th><th>Full Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Registered</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['fullname']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['phone']) ?></td>
                                <td>
                                    <form method="POST" action="<?= BASE_URL ?>index.php?page=admin/users&action=update_role" style="display:inline-block">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <select name="role" class="form-select form-select-sm" onchange="this.form.submit()" <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                            <option value="student" <?= $user['role']=='student'?'selected':'' ?>>Student</option>
                                            <option value="staff" <?= $user['role']=='staff'?'selected':'' ?>>Staff</option>
                                            <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
                                        </select>
                                    </form>
                                </td>
                                <td><?= date('Y-m-d', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $user['id'] ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form method="POST" action="<?= BASE_URL ?>index.php?page=admin/users&action=delete" style="display:inline-block" onsubmit="return confirm('Delete this user? All their posts will be removed.')">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Current</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            
                            <!-- Edit User Modal for each user -->
                            <div class="modal fade" id="editUserModal<?= $user['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5>Edit User: <?= htmlspecialchars($user['fullname']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="<?= BASE_URL ?>index.php?page=admin/users&action=edit" class="needs-validation" novalidate>
                                            <div class="modal-body">
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <div class="mb-2">
                                                    <label>Full Name</label>
                                                    <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user['fullname']) ?>" minlength="2" maxlength="100" autocomplete="name" required>
                                                    <div class="invalid-feedback">Enter the user's full name.</div>
                                                </div>
                                                <div class="mb-2">
                                                    <label>Email</label>
                                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" maxlength="150" autocomplete="email" required>
                                                    <div class="invalid-feedback">Enter a valid email address.</div>
                                                </div>
                                                <div class="mb-2">
                                                    <label>Phone</label>
                                                    <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" inputmode="tel" autocomplete="tel" maxlength="20" pattern="^\+?[0-9\s().-]{7,20}$" title="Use 7 to 15 digits, optionally starting with +. Spaces, dashes, and parentheses are allowed.">
                                                    <div class="invalid-feedback">Enter a valid phone number, for example +255712345678.</div>
                                                </div>
                                                <div class="mb-2">
                                                    <label>Role</label>
                                                    <select name="role" class="form-select">
                                                        <option value="student" <?= $user['role']=='student'?'selected':'' ?>>Student</option>
                                                        <option value="staff" <?= $user['role']=='staff'?'selected':'' ?>>Staff</option>
                                                        <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label>New Password (leave blank to keep current)</label>
                                                    <input type="password" name="password" class="form-control" placeholder="Enter only if changing" minlength="6" autocomplete="new-password">
                                                    <div class="invalid-feedback">Password must be at least 6 characters.</div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5>Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>index.php?page=admin/users&action=add" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="mb-2">
                        <label>Full Name *</label>
                        <input type="text" name="fullname" class="form-control" minlength="2" maxlength="100" autocomplete="name" required>
                        <div class="invalid-feedback">Enter the user's full name.</div>
                    </div>
                    <div class="mb-2">
                        <label>Email *</label>
                        <input type="email" name="email" class="form-control" maxlength="150" autocomplete="email" required>
                        <div class="invalid-feedback">Enter a valid email address.</div>
                    </div>
                    <div class="mb-2">
                        <label>Phone</label>
                        <input type="tel" name="phone" class="form-control" inputmode="tel" autocomplete="tel" maxlength="20" pattern="^\+?[0-9\s().-]{7,20}$" title="Use 7 to 15 digits, optionally starting with +. Spaces, dashes, and parentheses are allowed.">
                        <div class="invalid-feedback">Enter a valid phone number, for example +255712345678.</div>
                    </div>
                    <div class="mb-2">
                        <label>Password *</label>
                        <input type="password" name="password" class="form-control" minlength="6" autocomplete="new-password" required>
                        <div class="invalid-feedback">Password must be at least 6 characters.</div>
                    </div>
                    <div class="mb-2">
                        <label>Confirm Password *</label>
                        <input type="password" name="confirm_password" class="form-control" minlength="6" autocomplete="new-password" required>
                        <div class="invalid-feedback">Confirm the password.</div>
                    </div>
                    <div class="mb-2">
                        <label>Role</label>
                        <select name="role" class="form-select">
                            <option value="student">Student</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add User</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
