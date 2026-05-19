<?php
// controllers/AuthController.php
require_once __DIR__ . '/../includes/config.php';

class AuthController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function login($email, $password)
    {
        $stmt = $this->pdo->prepare("SELECT id, fullname, email, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
        return false;
    }

    public function register($fullname, $email, $password, $phone = '')
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (fullname, email, password, phone, role) VALUES (?, ?, ?, ?, 'student')");
        return $stmt->execute([$fullname, $email, $hashed, $phone]);
    }

    public function logout()
    {
        session_destroy();
        redirect('login');
    }

    public function requireLogin()
    {
        if (!isLoggedIn())
            redirect('login');
    }

    public function requireAdmin()
    {
        if (!isAdmin())
            redirect('dashboard');
    }
}

// Handle actions based on query parameter 'action'
$action = $_GET['action'] ?? '';

$auth = new AuthController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        if ($auth->login($email, $password)) {
            if ($_SESSION['role'] === 'admin') {
                redirect('admin/dashboard');
            } else {
                redirect('dashboard');
            }
        } else {
            $_SESSION['error'] = "Invalid email or password";
            redirect('login');
        }
    } elseif ($action === 'register') {
        $fullname = $_POST['fullname'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $phone = $_POST['phone'] ?? '';

        if ($password !== $confirm) {
            $_SESSION['errors'] = ["Passwords do not match"];
            redirect('register');
        }
        if (strlen($password) < 6) {
            $_SESSION['errors'] = ["Password must be at least 6 characters"];
            redirect('register');
        }
        if ($auth->register($fullname, $email, $password, $phone)) {
            $_SESSION['success'] = "Registration successful. Please login.";
            redirect('login');
        } else {
            $_SESSION['errors'] = ["Registration failed. Email may already exist."];
            redirect('register');
        }
    }
} else {
    // Show the appropriate form (GET request)
    if ($action === 'login' || $page === 'login') {
        require_once __DIR__ . '/../views/auth/login.php';
    } elseif ($action === 'register' || $page === 'register') {
        require_once __DIR__ . '/../views/auth/register.php';
    } elseif ($action === 'logout') {
        $auth->logout();
    }
}
?>