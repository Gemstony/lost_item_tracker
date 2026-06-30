<?php
// controllers/AuthController.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/helpers.php';

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
        redirect('index.php?page=login');
    }

    public function requireLogin()
    {
        if (!isLoggedIn())
            redirect('index.php?page=login');
    }

    public function requireAdmin()
    {
        if (!isAdmin())
            redirect('index.php?page=dashboard');
    }
}

// Handle actions based on query parameter 'action'
$action = $_GET['action'] ?? '';

$auth = new AuthController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $_SESSION['error'] = "Enter a valid email and password.";
            redirect('index.php?page=login');
        }

        if ($auth->login($email, $password)) {
            if ($_SESSION['role'] === 'admin') {
                redirect('index.php?page=admin/dashboard');
            } else {
                redirect('index.php?page=dashboard');
            }
        } else {
            $_SESSION['error'] = "Invalid email or password";
            redirect('index.php?page=login');
        }
    } elseif ($action === 'register') {
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $phone = normalizePhone($_POST['phone'] ?? '');

        $errors = validateUserForm($_POST);

        if ($errors) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = [
                'fullname' => $fullname,
                'email' => $email,
                'phone' => $_POST['phone'] ?? '',
            ];
            redirect('index.php?page=register');
        }

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['errors'] = ["Email already exists."];
            $_SESSION['old'] = [
                'fullname' => $fullname,
                'email' => $email,
                'phone' => $_POST['phone'] ?? '',
            ];
            redirect('index.php?page=register');
        }

        if ($auth->register($fullname, $email, $password, $phone)) {
            $_SESSION['success'] = "Registration successful. Please login.";
            redirect('index.php?page=login');
        } else {
            $_SESSION['errors'] = ["Registration failed. Email may already exist."];
            redirect('index.php?page=register');
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
