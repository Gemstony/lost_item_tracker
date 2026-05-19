<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-success">
                <h5>Welcome, Admin <?= htmlspecialchars($_SESSION['fullname']) ?></h5>
                <p>This is the admin control panel. From here you can manage users, view reports, and run matching.</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <h5><i class="fas fa-users"></i> Manage Users</h5>
                    <p>View, edit, or delete user accounts.</p>
                    <a href="<?= BASE_URL ?>index.php?page=admin/users" class="btn btn-light">Go</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <h5><i class="fas fa-chart-line"></i> System Reports</h5>
                    <p>Generate analytics and export reports.</p>
                    <a href="<?= BASE_URL ?>index.php?page=admin/reports" class="btn btn-light">Go</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-warning h-100">
                <div class="card-body">
                    <h5><i class="fas fa-robot"></i> Matching Engine</h5>
                    <p>Run automated matching between lost and found items.</p>
                    <a href="<?= BASE_URL ?>index.php?page=matches&action=run" class="btn btn-light" onclick="return confirm('Run matching now?')">Run Matching</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>