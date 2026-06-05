<?php
// index.php - Entry point
require_once __DIR__ . '/includes/config.php';
// require_once __DIR__ . '/controllers/AuthController.php';

// $auth = new AuthController($pdo);

// Get the page parameter (default to 'welcome')
$page = $_GET['page'] ?? 'welcome';

// Routing based on page parameter
switch ($page) {
    // Public pages (no login required)
    case 'welcome':
        require_once __DIR__ . '/welcome.php';
        break;

    case 'login':
    case 'register':
    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        break;

    // Protected pages (require login)
    case 'dashboard':
        if (!isLoggedIn())
            redirect('login');
        require_once __DIR__ . '/views/dashboard/index.php';
        break;

    case 'lost_items/report':
        if (!isLoggedIn())
            redirect('login');
        require_once __DIR__ . '/controllers/LostItemController.php';
        break;

    case 'lost_items/list':
        if (!isLoggedIn())
            redirect('login');
        require_once __DIR__ . '/views/lost_items/list.php';
        break;

    case 'found_items/report':
        if (!isLoggedIn())
            redirect('login');
        require_once __DIR__ . '/controllers/FoundItemController.php';
        break;

    case 'found_items/list':
        if (!isLoggedIn())
            redirect('login');
        require_once __DIR__ . '/views/found_items/list.php';
        break;

    case 'found_items/map':
        if (!isLoggedIn())
            redirect('login');
        require_once __DIR__ . '/views/found_items/map_view.php';
        break;

    case 'incidents/report':
        if (!isLoggedIn())
            redirect('login');
        require_once __DIR__ . '/controllers/IncidentController.php';
        break;

    case 'incidents':
        if (!isLoggedIn())
            redirect('login');
        require_once __DIR__ . '/controllers/IncidentController.php';
        break;

    case 'incidents/list':
        if (!isLoggedIn())
            redirect('login');
        require_once __DIR__ . '/views/incidents/list.php';
        break;

    case 'incidents/update':
        if (!isAdmin())
            redirect('dashboard');
        require_once __DIR__ . '/controllers/IncidentController.php';
        break;

    case 'matches':
    case 'matches/view':
        if (!isLoggedIn())
            redirect('login');
        require_once __DIR__ . '/controllers/MatchController.php';
        break;

    case 'admin/dashboard':
        if (!isAdmin())
            redirect('dashboard');
        require_once __DIR__ . '/views/admin/dashboard.php';
        break;

    // Admin user management CRUD
    case 'admin/users':
        if (!isAdmin())
            redirect('dashboard');

        $action = $_GET['action'] ?? '';

        // Add user
        if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $password = $_POST['password'];
            $confirm = $_POST['confirm_password'];
            $role = $_POST['role'];

            if ($password !== $confirm) {
                $_SESSION['error'] = "Passwords do not match";
                redirect('index.php?page=admin/users');
            }
            if (strlen($password) < 6) {
                $_SESSION['error'] = "Password must be at least 6 characters";
                redirect('index.php?page=admin/users');
            }
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = "Email already exists";
                redirect('index.php?page=admin/users');
            }

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (fullname, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$fullname, $email, $phone, $hashed, $role])) {
                $_SESSION['success'] = "User added successfully";
            } else {
                $_SESSION['error'] = "Failed to add user";
            }
            redirect('index.php?page=admin/users');
        }

        // Edit user
        elseif ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'];
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $role = $_POST['role'];
            $newPassword = $_POST['password'];

            // Prevent editing own role to non-admin? We'll allow but caution.
            if ($userId == $_SESSION['user_id'] && $role != 'admin') {
                $_SESSION['error'] = "You cannot change your own admin role.";
                redirect('index.php?page=admin/users');
            }

            $updates = "fullname = ?, email = ?, phone = ?, role = ?";
            $params = [$fullname, $email, $phone, $role];

            if (!empty($newPassword)) {
                if (strlen($newPassword) < 6) {
                    $_SESSION['error'] = "Password must be at least 6 characters";
                    redirect('index.php?page=admin/users');
                }
                $updates .= ", password = ?";
                $params[] = password_hash($newPassword, PASSWORD_DEFAULT);
            }
            $params[] = $userId;

            $stmt = $pdo->prepare("UPDATE users SET $updates WHERE id = ?");
            if ($stmt->execute($params)) {
                $_SESSION['success'] = "User updated successfully";
                // If editing own data, update session variables
                if ($userId == $_SESSION['user_id']) {
                    $_SESSION['fullname'] = $fullname;
                    $_SESSION['email'] = $email;
                    // Role change? If admin changes own role, might lock out – we don't allow changing own role above.
                }
            } else {
                $_SESSION['error'] = "Update failed";
            }
            redirect('index.php?page=admin/users');
        }

        // Delete user
        elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'];
            if ($userId == $_SESSION['user_id']) {
                $_SESSION['error'] = "You cannot delete your own account";
                redirect('index.php?page=admin/users');
            }
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            if ($stmt->execute([$userId])) {
                $_SESSION['success'] = "User deleted";
            } else {
                $_SESSION['error'] = "Delete failed";
            }
            redirect('index.php?page=admin/users');
        }

        // Update role (inline dropdown)
        elseif ($action === 'update_role' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'];
            $role = $_POST['role'];
            if ($userId == $_SESSION['user_id']) {
                $_SESSION['error'] = "You cannot change your own role";
                redirect('index.php?page=admin/users');
            }
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            if ($stmt->execute([$role, $userId])) {
                $_SESSION['success'] = "Role updated";
            } else {
                $_SESSION['error'] = "Update failed";
            }
            redirect('index.php?page=admin/users');
        }

        // Default: show user list
        require_once __DIR__ . '/views/admin/users.php';
        break;

    // case 'admin/reports':
    //     if (!isAdmin())
    //         redirect('dashboard');
    //     require_once __DIR__ . '/controllers/ReportController.php';
    //     break;

    // Admin PDF reports (global)
    case 'admin/reports/pdf':
        if (!isAdmin())
            redirect('dashboard');
        require_once __DIR__ . '/controllers/ReportController.php';
        break;

    // User PDF reports (personal)
    case 'user/reports/pdf':
        if (!isLoggedIn())
            redirect('login');
        require_once __DIR__ . '/controllers/ReportController.php';
        break;

    case 'admin/reports':
        if (!isAdmin())
            redirect('dashboard');
        require_once __DIR__ . '/views/admin/reports.php';
        break;

    case 'lost_items/map':
        if (!isLoggedIn()) redirect('login');
        require_once __DIR__ . '/views/lost_items/map_view.php';
        break;
    // User profile management
    case 'profile':
        if (!isLoggedIn())
            redirect('login');

        $action = $_GET['action'] ?? '';

        // Update profile details
        if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $userId = $_SESSION['user_id'];

            // Check if email is already used by another user
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                $_SESSION['profile_error'] = "Email already in use by another account.";
                redirect('index.php?page=profile');
            }

            $stmt = $pdo->prepare("UPDATE users SET fullname = ?, email = ?, phone = ? WHERE id = ?");
            if ($stmt->execute([$fullname, $email, $phone, $userId])) {
                $_SESSION['fullname'] = $fullname;
                $_SESSION['email'] = $email;
                $_SESSION['profile_success'] = "Profile updated successfully.";
            } else {
                $_SESSION['profile_error'] = "Update failed.";
            }
            redirect('index.php?page=profile');
        }

        // Change password
        elseif ($action === 'changepassword' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $current = $_POST['current_password'];
            $new = $_POST['new_password'];
            $confirm = $_POST['confirm_password'];
            $userId = $_SESSION['user_id'];

            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!password_verify($current, $user['password'])) {
                $_SESSION['profile_error'] = "Current password is incorrect.";
                redirect('index.php?page=profile');
            }
            if (strlen($new) < 6) {
                $_SESSION['profile_error'] = "New password must be at least 6 characters.";
                redirect('index.php?page=profile');
            }
            if ($new !== $confirm) {
                $_SESSION['profile_error'] = "New passwords do not match.";
                redirect('index.php?page=profile');
            }

            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt->execute([$hashed, $userId])) {
                $_SESSION['profile_success'] = "Password changed successfully.";
            } else {
                $_SESSION['profile_error'] = "Password change failed.";
            }
            redirect('index.php?page=profile');
        }

        // Show profile page
        require_once __DIR__ . '/views/profile/index.php';
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Page not found</h1>";
        break;
}
?>