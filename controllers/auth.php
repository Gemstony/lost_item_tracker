<?php
// controllers/auth.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';

// Determine action from URL parameter
$action = $_GET['action'] ?? 'login';

if ($action === 'register') {
    // Handle registration form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullname = sanitize($_POST['fullname']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $phone = sanitize($_POST['phone'] ?? '');
        $role = 'student'; // default role, can be changed by admin later

        // Validation
        $errors = [];
        if (empty($fullname)) $errors[] = "Full name is required";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
        if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
        if ($password !== $confirm_password) $errors[] = "Passwords do not match";

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors[] = "Email already registered";

        if (empty($errors)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$fullname, $email, $hashed_password, $phone, $role])) {
                $_SESSION['success'] = "Registration successful! Please login.";
                redirect("index.php?page=login");
            } else {
                $errors[] = "Registration failed. Please try again.";
            }
        }
        // Store errors in session to display on the form
        $_SESSION['errors'] = $errors;
        redirect("index.php?page=register");
    }
    // Show registration form
    require_once __DIR__ . '/../views/auth/register.php';
} 
elseif ($action === 'login') {
    // Handle login form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT id, fullname, email, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            redirect("index.php?page=dashboard/index");
        } else {
            $_SESSION['error'] = "Invalid email or password";
            redirect("index.php?page=login");
        }
    }
    // Show login form
    require_once __DIR__ . '/../views/auth/login.php';
}
elseif ($action === 'logout') {
    session_destroy();
    redirect("index.php?page=login");
}
?>