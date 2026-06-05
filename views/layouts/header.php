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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <!-- Improved Custom Style -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Wrapper */
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ========== SIDEBAR STYLES ========== */
        #sidebar {
            min-width: 280px;
            max-width: 280px;
            background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
            color: #e9ecef;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        #sidebar.active {
            margin-left: -280px;
        }

        #sidebar .sidebar-header {
            padding: 1.5rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(0, 0, 0, 0.1);
        }

        #sidebar .sidebar-header h4 {
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: white;
        }

        #sidebar .sidebar-header small {
            font-size: 0.75rem;
            opacity: 0.8;
        }

        #sidebar ul.components {
            padding: 1rem 0;
            flex-grow: 1;
        }

        #sidebar ul li {
            list-style: none;
        }

        #sidebar ul li a {
            padding: 0.75rem 1.2rem;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #e9ecef;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        #sidebar ul li a i {
            width: 24px;
            font-size: 1.1rem;
            text-align: center;
        }

        #sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.1);
            border-left-color: #ffc107;
            color: white;
        }

        #sidebar ul li.active a {
            background: rgba(255, 255, 255, 0.15);
            border-left-color: #ffc107;
            color: white;
        }

        #sidebar .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            text-align: center;
            margin-top: auto;
        }

        #sidebar .sidebar-footer a {
            color: #ffc107;
            text-decoration: none;
            font-weight: 500;
        }

        /* ========== CONTENT AREA ========== */
        #content {
            width: 100%;
            padding: 1.2rem 1.8rem;
            transition: all 0.3s;
            background-color: #f0f2f5;
        }

        /* Top Navbar */
        .navbar {
            background: white !important;
            border-radius: 16px;
            padding: 0.7rem 1.5rem;
            margin-bottom: 1.8rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .navbar .navbar-text {
            font-weight: 500;
            color: #2c3e50;
        }

        .notification-bell {
            position: relative;
            margin-right: 15px;
            color: #2c3e50;
            font-size: 1.2rem;
            transition: color 0.2s;
        }

        .notification-bell:hover {
            color: #0d6efd;
        }

        .badge-notify {
            position: absolute;
            top: -8px;
            right: -10px;
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            border-radius: 50%;
        }

        /* Page content card style */
        .page-content .card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .page-content .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            padding: 1rem 1.2rem;
            border-radius: 16px 16px 0 0;
        }

        footer {
            background: white;
            border-radius: 16px;
            padding: 1rem;
            text-align: center;
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 2rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -280px;
            }

            #sidebar.active {
                margin-left: 0;
            }

            #content {
                padding: 1rem;
            }

            .navbar .btn-outline-secondary span {
                display: none;
            }
        }

        /* Button toggle */
        #sidebarCollapse {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 0.4rem 1rem;
            transition: all 0.2s;
        }

        #sidebarCollapse:hover {
            background: #f8f9fa;
            border-color: #cbd3da;
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
                    <?php if ($userRole === 'student' || $userRole === 'staff'): ?>
                        <li><a href="<?= BASE_URL ?>index.php?page=dashboard"><i class="fas fa-tachometer-alt"></i>
                                Dashboard</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?page=lost_items/report"><i class="fas fa-frown"></i> Report Lost
                                Item</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?page=found_items/report"><i class="fas fa-smile"></i> Report Found
                                Item</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?page=lost_items/list"><i class="fas fa-list"></i> View Lost
                                Items</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?page=found_items/list"><i class="fas fa-map-marker-alt"></i> View
                                Found Items</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?page=incidents/report"><i class="fas fa-exclamation-triangle"></i>
                                Report Incident</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?page=matches/view"><i class="fas fa-handshake"></i> My Matches</a>
                        </li>
                    <?php endif; ?>

                    <?php if ($userRole === 'admin'): ?>
                        <li><a href="<?= BASE_URL ?>index.php?page=admin/dashboard"><i class="fas fa-chart-line"></i> Admin
                                Panel</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?page=admin/all_lost_items"><i class="fas fa-frown"></i> All Lost
                                Items</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?page=admin/all_found_items"><i class="fas fa-smile"></i> All Found
                                Items</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?page=admin/all_matches"><i class="fas fa-handshake"></i> All
                                Matches</a></li>
                    <?php endif; ?>

                    <li><a href="<?= BASE_URL ?>index.php?page=incidents/list"><i class="fas fa-list"></i> View
                            Incidents</a></li>

                    <?php if ($userRole === 'admin'): ?>
                        <li><a href="<?= BASE_URL ?>index.php?page=admin/users"><i class="fas fa-users"></i> Manage Users</a>
                        </li>
                        <li><a href="<?= BASE_URL ?>index.php?page=admin/reports"><i class="fas fa-file-alt"></i> System
                                Reports</a></li>
                    <?php endif; ?>

                    <li><a href="<?= BASE_URL ?>index.php?page=profile"><i class="fas fa-user-circle"></i> My Profile</a>
                    </li>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>index.php?page=login"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <?php endif; ?>
            </ul>
            <div class="sidebar-footer">
                <a href="<?= BASE_URL ?>index.php?page=logout&action=logout"><i class="fas fa-sign-out-alt"></i>
                    Logout</a>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-outline-secondary">
                        <i class="fas fa-bars"></i> <span>Toggle Menu</span>
                    </button>
                    <div class="ms-auto d-flex align-items-center">
                        <a href="<?= BASE_URL ?>index.php?page=matches/view" class="notification-bell"
                            title="Notifications">
                            <i class="fas fa-bell"></i>
                            <?php if (isset($unreadCount) && $unreadCount > 0): ?>
                                <span class="badge bg-danger badge-notify"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="navbar-text ms-3">
                            <i class="fas fa-user-circle"></i>
                            <?= htmlspecialchars($userName) ?> (<?= ucfirst($userRole) ?>)
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main content starts here -->
            <div class="page-content">