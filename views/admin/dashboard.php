<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../layouts/header.php';

// Get statistics
$stats = [];

// Total users
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$stats['users'] = $stmt->fetch()['total'];

// Total lost items
$stmt = $pdo->query("SELECT COUNT(*) as total FROM lost_items");
$stats['lost_items'] = $stmt->fetch()['total'];

// Total found items
$stmt = $pdo->query("SELECT COUNT(*) as total FROM found_items");
$stats['found_items'] = $stmt->fetch()['total'];

// Total incidents
$stmt = $pdo->query("SELECT COUNT(*) as total FROM incidents");
$stats['incidents'] = $stmt->fetch()['total'];

// Pending matches
$stmt = $pdo->query("SELECT COUNT(*) as total FROM matches WHERE status = 'pending'");
$stats['pending_matches'] = $stmt->fetch()['total'];

// Resolved matches
$stmt = $pdo->query("SELECT COUNT(*) as total FROM matches WHERE status = 'resolved'");
$stats['resolved_matches'] = $stmt->fetch()['total'];

// Lost/found by category (for chart)
$stmt = $pdo->query("
    SELECT category, COUNT(*) as count FROM lost_items 
    WHERE category IS NOT NULL AND category != '' 
    GROUP BY category ORDER BY count DESC LIMIT 5
");
$lostCategories = $stmt->fetchAll();

$stmt = $pdo->query("
    SELECT incident_type, COUNT(*) as count FROM incidents 
    GROUP BY incident_type
");
$incidentTypes = $stmt->fetchAll();
?>

<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <h3><?= $stats['users'] ?></h3>
                    <h6><i class="fas fa-users"></i> Total Users</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning h-100">
                <div class="card-body">
                    <h3><?= $stats['lost_items'] ?></h3>
                    <h6><i class="fas fa-frown"></i> Lost Items</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <h3><?= $stats['found_items'] ?></h3>
                    <h6><i class="fas fa-smile"></i> Found Items</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-danger h-100">
                <div class="card-body">
                    <h3><?= $stats['incidents'] ?></h3>
                    <h6><i class="fas fa-exclamation-triangle"></i> Incidents</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6><i class="fas fa-chart-pie"></i> Lost Items by Category</h6>
                </div>
                <div class="card-body">
                    <canvas id="lostChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6><i class="fas fa-chart-pie"></i> Incidents by Type</h6>
                </div>
                <div class="card-body">
                    <canvas id="incidentChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6><i class="fas fa-handshake"></i> Match Stats</h6>
                </div>
                <div class="card-body text-center">
                    <h3><?= $stats['pending_matches'] ?> <small class="text-muted">Pending</small></h3>
                    <h3><?= $stats['resolved_matches'] ?> <small class="text-muted">Resolved</small></h3>
                    <a href="<?= BASE_URL ?>index.php?page=matches&action=run" class="btn btn-warning btn-sm" onclick="return confirm('Run matching now?')">
                        <i class="fas fa-robot"></i> Run Matching
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6><i class="fas fa-file-alt"></i> Reports</h6>
                </div>
                <div class="card-body">
                    <a href="<?= BASE_URL ?>index.php?page=admin/reports&type=lost" class="btn btn-sm btn-outline-primary w-100 mb-2">Lost Items Report (PDF)</a>
                    <a href="<?= BASE_URL ?>index.php?page=admin/reports&type=found" class="btn btn-sm btn-outline-success w-100 mb-2">Found Items Report (PDF)</a>
                    <a href="<?= BASE_URL ?>index.php?page=admin/reports&type=incidents" class="btn btn-sm btn-outline-danger w-100">Incidents Report (PDF)</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h6><i class="fas fa-cog"></i> Quick Actions</h6>
                </div>
                <div class="card-body">
                    <a href="<?= BASE_URL ?>index.php?page=admin/users" class="btn btn-primary w-100 mb-2">Manage Users</a>
                    <a href="<?= BASE_URL ?>index.php?page=incidents/list" class="btn btn-danger w-100">View All Incidents</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Lost items by category chart
const lostCtx = document.getElementById('lostChart').getContext('2d');
new Chart(lostCtx, {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_column($lostCategories, 'category')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($lostCategories, 'count')) ?>,
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8']
        }]
    }
});

// Incident types chart
const incCtx = document.getElementById('incidentChart').getContext('2d');
new Chart(incCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($incidentTypes, 'incident_type')) ?>,
        datasets: [{
            label: 'Number of Incidents',
            data: <?= json_encode(array_column($incidentTypes, 'count')) ?>,
            backgroundColor: '#dc3545'
        }]
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>