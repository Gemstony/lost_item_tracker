<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default page title
$pageTitle = $pageTitle ?? 'Digital Tracking & Reporting System';

// Get user role from session (student, staff, admin)
$userRole = $_SESSION['role'] ?? 'guest';
$userName = $_SESSION['fullname'] ?? 'Guest';

if (isLoggedIn()) {
    require_once __DIR__ . '/../../models/Notification.php';
    $notifModel = new NotificationModel($pdo);
    $unreadCount = count($notifModel->getUnread($_SESSION['user_id']));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Bootstrap 5 CSS + Icons + Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Leaflet.js for maps (GPS feature) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <!-- Custom style -->
    <style>
        /* Simple sidebar styling (you can move to style.css later) */
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #0d6efd;
            color: #fff;
            transition: all 0.3s;
            min-height: 100vh;
        }
        #sidebar.active {
            margin-left: -250px;
        }
        #sidebar .sidebar-header {
            padding: 20px;
            background: #0b5ed7;
            text-align: center;
        }
        #sidebar ul.components {
            padding: 20px 0;
        }
        #sidebar ul li a {
            padding: 10px 20px;
            font-size: 1.1em;
            display: block;
            color: white;
            text-decoration: none;
        }
        #sidebar ul li a:hover {
            background: #0b5ed7;
        }
        #sidebar ul li a i {
            margin-right: 10px;
        }
        #sidebar .sidebar-footer {
            position: absolute;
            bottom: 0;
            min-width: 250px;
            max-width: 250px;
            background: #0b5ed7;
        }
        #content {
            width: 100%;
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
            background-color: #f8f9fc;
        }
        .navbar {
            background: white !important;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        footer {
            background: white;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin-top: 30px;
            font-size: 0.9rem;
            color: #6c757d;
        }
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }
            #sidebar.active {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-search"></i> Lost & Found</h4>
            <small>Digital Tracking System</small>
        </div>
        <ul class="list-unstyled components">
            <?php if ($userRole === 'admin' || $userRole === 'staff' || $userRole === 'student'): ?>
                <li>
                    <a href="<?= BASE_URL ?>index.php?page=dashboard">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>index.php?page=lost_items/report">
                        <i class="fas fa-frown"></i> Report Lost Item
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>index.php?page=found_items/report">
                        <i class="fas fa-smile"></i> Report Found Item
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>index.php?page=lost_items/list">
                        <i class="fas fa-list"></i> View Lost Items
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>index.php?page=found_items/list">
                        <i class="fas fa-map-marker-alt"></i> View Found Items
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>index.php?page=incidents/report">
                        <i class="fas fa-exclamation-triangle"></i> Report Incident
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>index.php?page=incidents/list">
                        <i class="fas fa-list"></i> View Incidents
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>index.php?page=matches/view">
                        <i class="fas fa-handshake"></i> My Matches
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>index.php?page=profile">
                        <i class="fas fa-user-circle"></i> My Profile
                    </a>
                </li>
                <?php if ($userRole === 'admin'): ?>
                    <hr class="bg-light">
                    <li><a href="<?= BASE_URL ?>index.php?page=admin/dashboard"><i class="fas fa-chart-line"></i> Admin Panel</a></li>
                    <li><a href="<?= BASE_URL ?>index.php?page=admin/users"><i class="fas fa-users"></i> Manage Users</a></li>
                    <li><a href="<?= BASE_URL ?>index.php?page=admin/reports"><i class="fas fa-file-alt"></i> System Reports</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><a href="<?= BASE_URL ?>index.php?page=login"><i class="fas fa-sign-in-alt"></i> Login</a></li>
            <?php endif; ?>
            <li class="mt-4"><a href="<?= BASE_URL ?>index.php?page=logout&action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>

        </ul>

    </nav>

    <!-- Page Content -->
    <div id="content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-outline-secondary">
                    <i class="fas fa-bars"></i> <span>Toggle Menu</span>
                </button>
                <a href="<?= BASE_URL ?>index.php?page=matches/view" class="me-3 text-dark">
                    <i class="fas fa-bell"></i>
                    <?php if (isset($unreadCount) && $unreadCount > 0): ?>
                        <span class="badge bg-danger"><?= $unreadCount ?></span>
                    <?php endif; ?>
                </a>
                <div class="ms-auto">
                    <span class="navbar-text">
                        <i class="fas fa-user-circle"></i>
                        <?= htmlspecialchars($userName) ?> (<?= ucfirst($userRole) ?>)
                    </span>
                </div>
            </div>
        </nav>

        <!-- Main content starts here -->
        <div class="page-content">