<?php
// controllers/AuthController.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // ------------------- LOGIN -------------------
    public function login($email, $password)
    {
        $stmt = $this->pdo->prepare("SELECT id, fullname, email, password, role, is_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['error' => 'Invalid email or password.'];
        }

        if ($user['is_verified'] != 1) {
            return ['error' => 'Your email is not verified. Please Click the bellow button to Verify your Email.'];
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            return ['success' => true];
        }

        return ['error' => 'Invalid email or password.'];
    }

    // ------------------- REGISTER -------------------
    public function register($fullname, $email, $password, $phone = '')
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $otp = rand(100000, 999999);
        $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $stmt = $this->pdo->prepare("
            INSERT INTO users (fullname, email, password, phone, role, otp_code, otp_expires_at, is_verified)
            VALUES (?, ?, ?, ?, 'student', ?, ?, 0)
        ");
        $success = $stmt->execute([$fullname, $email, $hashed, $phone, $otp, $expiry]);

        if ($success) {
            if ($this->sendOTPEmail($email, $otp)) {
                return ['success' => true, 'otp_sent' => true];
            } else {
                // Rollback user creation if email fails
                $this->pdo->prepare("DELETE FROM users WHERE email = ?")->execute([$email]);
                return ['error' => 'Failed to send OTP. Please try again.'];
            }
        }
        return ['error' => 'Registration failed. Email may already exist.'];
    }

    // ------------------- SEND OTP EMAIL -------------------
    private function sendOTPEmail($email, $otp)
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'jmwita424@gmail.com';
            $mail->Password = 'iczw utns cjzr qnjw'; // app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('jmwita424@gmail.com', 'Lost & Found System');
            $mail->addAddress($email);
            $mail->isHTML(false);
            $mail->Subject = 'Verify Your Email - Lost & Found System';
            $mail->Body = "Your verification OTP is: $otp\n\nThis code expires in 10 minutes.\n\nIf you did not register, ignore this email.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("OTP email failed: " . $mail->ErrorInfo);
            return false;
        }
    }

    // ------------------- VERIFY OTP -------------------
    public function verifyOTP($email, $otp)
    {
        $stmt = $this->pdo->prepare("SELECT id, otp_code, otp_expires_at, is_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['error' => 'User not found.'];
        }

        if ($user['is_verified'] == 1) {
            return ['error' => 'Email already verified. Please login.'];
        }

        if (strtotime($user['otp_expires_at']) < time()) {
            return ['error' => 'OTP has expired. Please request a new one.'];
        }

        if ($otp != $user['otp_code']) {
            return ['error' => 'Invalid OTP.'];
        }

        // Verify user
        $update = $this->pdo->prepare("UPDATE users SET is_verified = 1, otp_code = NULL, otp_expires_at = NULL WHERE id = ?");
        $update->execute([$user['id']]);

        // Auto-login
        $stmt = $this->pdo->prepare("SELECT fullname, email, role FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $data = $stmt->fetch();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $data['fullname'];
        $_SESSION['email'] = $data['email'];
        $_SESSION['role'] = $data['role'];

        // Set SweetAlert flash for dashboard
        $_SESSION['swal'] = [
            'title' => 'Email Verified!',
            'text' => 'Your email has been successfully verified. Welcome to the system!',
            'icon' => 'success'
        ];

        return ['success' => true];
    }

    // ------------------- RESEND OTP (existing user) -------------------
    public function resendOTP($email)
    {
        $stmt = $this->pdo->prepare("SELECT id, is_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || $user['is_verified'] == 1) {
            return ['error' => 'Invalid request or email already verified.'];
        }

        $otp = rand(100000, 999999);
        $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $update = $this->pdo->prepare("UPDATE users SET otp_code = ?, otp_expires_at = ? WHERE id = ?");
        $update->execute([$otp, $expiry, $user['id']]);

        if ($this->sendOTPEmail($email, $otp)) {
            return ['success' => true, 'message' => 'New OTP sent to your email.'];
        }
        return ['error' => 'Failed to send OTP. Try again later.'];
    }

    // ------------------- LOGOUT -------------------
    public function logout()
    {
        session_destroy();
        redirect('index.php?page=login');
    }
}

// ============ ROUTING ============
$action = $_GET['action'] ?? '';
$page = $_GET['page'] ?? '';
$auth = new AuthController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ----- LOGIN -----
    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $result = $auth->login($email, $password);
        if (isset($result['success'])) {
            if ($_SESSION['role'] === 'admin') {
                redirect('index.php?page=admin/dashboard');
            } else {
                redirect('index.php?page=dashboard');
            }
        } else {
            $_SESSION['error'] = $result['error'];
            redirect('index.php?page=login');
        }
    }

    // ----- REGISTER -----
    elseif ($action === 'register') {
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

        $result = $auth->register($fullname, $email, $password, $phone);
        if (isset($result['error'])) {
            $_SESSION['errors'] = [$result['error']];
            $_SESSION['old'] = [
                'fullname' => $fullname,
                'email' => $email,
                'phone' => $_POST['phone'] ?? '',
            ];
            redirect('index.php?page=register');
        }

        // Success: redirect to verification page with email
        $_SESSION['verify_email'] = $email;
        $_SESSION['success'] = "Registration successful! Check your email for OTP.";
        redirect('index.php?page=verify');
    }

    // ----- VERIFY OTP -----
    elseif ($action === 'verify') {
        $email = trim($_POST['email'] ?? '');
        $otp = trim($_POST['otp'] ?? '');
        if (!$email || !$otp) {
            $_SESSION['error'] = "Email and OTP are required.";
            redirect('index.php?page=verify');
        }
        $result = $auth->verifyOTP($email, $otp);
        if (isset($result['success'])) {
            // Redirect to dashboard; SweetAlert will appear there
            if ($_SESSION['role'] === 'admin') {
                redirect('index.php?page=admin/dashboard');
            } else {
                redirect('index.php?page=dashboard');
            }
        } else {
            $_SESSION['error'] = $result['error'];
            redirect('index.php?page=verify&email=' . urlencode($email));
        }
    }

    // ----- RESEND OTP (from verify page) -----
    elseif ($action === 'resend') {
        $email = trim($_POST['email'] ?? '');
        if (!$email) {
            $_SESSION['error'] = "Email is required.";
            redirect('index.php?page=verify');
        }
        $result = $auth->resendOTP($email);
        if (isset($result['success'])) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['error'];
        }
        redirect('index.php?page=verify&email=' . urlencode($email));
    }

    // ----- RESEND VERIFICATION (from resend_verification page) -----
    elseif ($action === 'send' && $page === 'resend_verification') {
        $email = trim($_POST['email'] ?? '');
        if (!$email) {
            $_SESSION['error'] = "Email is required.";
            redirect('index.php?page=resend_verification');
        }
        $stmt = $pdo->prepare("SELECT id, is_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user) {
            $_SESSION['error'] = "No account found with this email.";
            redirect('index.php?page=resend_verification');
        }
        if ($user['is_verified'] == 1) {
            $_SESSION['error'] = "This email is already verified. Please login.";
            redirect('index.php?page=login');
        }
        $result = $auth->resendOTP($email);
        if (isset($result['success'])) {
            $_SESSION['success'] = $result['message'];
            redirect('index.php?page=verify&email=' . urlencode($email));
        } else {
            $_SESSION['error'] = $result['error'];
            redirect('index.php?page=resend_verification');
        }
    }
}
else {
    // GET requests
    if ($action === 'login' || $page === 'login') {
        require_once __DIR__ . '/../views/auth/login.php';
    }
    elseif ($action === 'register' || $page === 'register') {
        require_once __DIR__ . '/../views/auth/register.php';
    }
    elseif ($action === 'verify' || $page === 'verify') {
        require_once __DIR__ . '/../views/auth/verify.php';
    }
    elseif ($action === 'resend_verification' || $page === 'resend_verification') {
        require_once __DIR__ . '/../views/auth/resend_verification.php';
    }
    elseif ($action === 'logout') {
        $auth->logout();
    }
}
?>