<?php
$pageTitle = 'User Dashboard';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            Welcome, <strong><?= htmlspecialchars($_SESSION['fullname']) ?></strong> (Role: <?= $_SESSION['role'] ?>)
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-frown"></i> Report Lost Item</h5>
                <p class="card-text">Lost something? Let the community help you.</p>
                <a href="index.php?page=lost_items/report" class="btn btn-light">Report Now</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-smile"></i> Report Found Item</h5>
                <p class="card-text">Found an item? Add GPS location and help return it.</p>
                <a href="index.php?page=found_items/report" class="btn btn-light">Report Found</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-exclamation-triangle"></i> Incident Report</h5>
                <p class="card-text">Report any safety or security incident.</p>
                <a href="index.php?page=incidents/report" class="btn btn-light">Report Incident</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>