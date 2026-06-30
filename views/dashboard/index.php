<?php
$pageTitle = 'User Dashboard';
require_once __DIR__ . '/../layouts/header.php';

// Get user-specific statistics
$userId = $_SESSION['user_id'];

// Count lost items
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM lost_items WHERE user_id = ?");
$stmt->execute([$userId]);
$lostCount = $stmt->fetch()['total'];

// Count found items
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM found_items WHERE user_id = ?");
$stmt->execute([$userId]);
$foundCount = $stmt->fetch()['total'];

// Count incidents
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM incidents WHERE user_id = ?");
$stmt->execute([$userId]);
$incidentCount = $stmt->fetch()['total'];

// Count pending matches involving user's lost or found items
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total FROM matches m
    JOIN lost_items l ON m.lost_item_id = l.id
    JOIN found_items f ON m.found_item_id = f.id
    WHERE (l.user_id = ? OR f.user_id = ?) AND m.status = 'pending'
");
$stmt->execute([$userId, $userId]);
$pendingMatches = $stmt->fetch()['total'];

// Count resolved matches
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total FROM matches m
    JOIN lost_items l ON m.lost_item_id = l.id
    JOIN found_items f ON m.found_item_id = f.id
    WHERE (l.user_id = ? OR f.user_id = ?) AND m.status = 'resolved'
");
$stmt->execute([$userId, $userId]);
$resolvedMatches = $stmt->fetch()['total'];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                Welcome, <strong><?= htmlspecialchars($_SESSION['fullname']) ?></strong> (Role: <?= $_SESSION['role'] ?>)
            </div>
        </div>
    </div>

    <!-- Personal Stats Cards -->
    <div class="row">
        <div class="col-md-3 mb-3">
            <a href="<?= BASE_URL ?>index.php?page=lost_items/list" class="dashboard-card-link" aria-label="View my lost items">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body text-center">
                        <h2><?= $lostCount ?></h2>
                        <h6><i class="fas fa-frown"></i> Lost Items</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="<?= BASE_URL ?>index.php?page=found_items/list" class="dashboard-card-link" aria-label="View my found items">
                <div class="card text-white bg-success h-100">
                    <div class="card-body text-center">
                        <h2><?= $foundCount ?></h2>
                        <h6><i class="fas fa-smile"></i> Found Items</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="<?= BASE_URL ?>index.php?page=incidents/list" class="dashboard-card-link" aria-label="View my incidents">
                <div class="card text-white bg-danger h-100">
                    <div class="card-body text-center">
                        <h2><?= $incidentCount ?></h2>
                        <h6><i class="fas fa-exclamation-triangle"></i> Incidents</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="<?= BASE_URL ?>index.php?page=matches/view" class="dashboard-card-link" aria-label="View my matches">
                <div class="card text-white bg-warning h-100">
                    <div class="card-body text-center">
                        <h2><?= $pendingMatches ?></h2>
                        <h6><i class="fas fa-handshake"></i> Pending Matches</h6>
                        <small><?= $resolvedMatches ?> resolved</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <a href="<?= BASE_URL ?>index.php?page=lost_items/report" class="dashboard-card-link" aria-label="Report a lost item">
                <div class="card border-primary h-100">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><i class="fas fa-frown"></i> Report Lost Item</h5>
                        <p class="card-text">Lost something? Let the community help you.</p>
                        <span class="btn btn-primary">Report Now</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="<?= BASE_URL ?>index.php?page=found_items/report" class="dashboard-card-link" aria-label="Report a found item">
                <div class="card border-success h-100">
                    <div class="card-body">
                        <h5 class="card-title text-success"><i class="fas fa-smile"></i> Report Found Item</h5>
                        <p class="card-text">Found an item? Add GPS location and help return it.</p>
                        <span class="btn btn-success">Report Found</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="<?= BASE_URL ?>index.php?page=incidents/report" class="dashboard-card-link" aria-label="Report an incident">
                <div class="card border-warning h-100">
                    <div class="card-body">
                        <h5 class="card-title text-warning"><i class="fas fa-exclamation-triangle"></i> Incident Report</h5>
                        <p class="card-text">Report any safety or security incident.</p>
                        <span class="btn btn-warning">Report Incident</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    Quick Access
                </div>
                <div class="card-body">
                    <a href="<?= BASE_URL ?>index.php?page=lost_items/list" class="btn btn-outline-primary">My Lost Items</a>
                    <a href="<?= BASE_URL ?>index.php?page=found_items/list" class="btn btn-outline-success">My Found Items</a>
                    <a href="<?= BASE_URL ?>index.php?page=incidents/list" class="btn btn-outline-danger">My Incidents</a>
                    <a href="<?= BASE_URL ?>index.php?page=matches/view" class="btn btn-outline-warning">My Matches</a>
                    <a href="<?= BASE_URL ?>index.php?page=profile" class="btn btn-outline-info">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
