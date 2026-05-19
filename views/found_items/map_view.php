<?php
// views/found_items/map_view.php
$pageTitle = 'Found Item Location';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../models/FoundItem.php';

// Get found item ID from URL
$itemId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($itemId <= 0) {
    echo "<div class='alert alert-danger'>Invalid item ID.</div>";
    require_once __DIR__ . '/../layouts/footer.php';
    exit;
}

$foundModel = new FoundItem($pdo);
$item = $foundModel->getById($itemId);

if (!$item) {
    echo "<div class='alert alert-danger'>Found item not found.</div>";
    require_once __DIR__ . '/../layouts/footer.php';
    exit;
}

// Check if GPS coordinates exist
if (empty($item['gps_latitude']) || empty($item['gps_longitude'])) {
    echo "<div class='alert alert-warning'>This item does not have GPS coordinates.</div>";
    require_once __DIR__ . '/../layouts/footer.php';
    exit;
}

$lat = $item['gps_latitude'];
$lng = $item['gps_longitude'];
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header bg-info text-white">
            <h5><i class="fas fa-map-marked-alt"></i> Found Item Location</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr><th>Item Name</th><td><?= htmlspecialchars($item['item_name']) ?></td></tr>
                        <tr><th>Category</th><td><?= htmlspecialchars($item['category']) ?></td></tr>
                        <tr><th>Description</th><td><?= nl2br(htmlspecialchars($item['description'])) ?></td></tr>
                        <tr><th>Found Location (Address)</th><td><?= htmlspecialchars($item['found_location']) ?></td></tr>
                        <tr><th>Found Date</th><td><?= $item['found_date'] ?></td></tr>
                        <tr><th>Reported By</th><td><?= htmlspecialchars($item['fullname']) ?> (<?= $item['email'] ?>)</td></tr>
                        <tr><th>GPS Coordinates</th><td><?= $lat . ', ' . $lng ?></td></tr>
                    </table>
                    
                    <!-- Navigation Button -->
                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $lat ?>,<?= $lng ?>" 
                       target="_blank" 
                       class="btn btn-primary btn-lg w-100 mb-2">
                        <i class="fas fa-directions"></i> Navigate with Google Maps
                    </a>
                    
                    <a href="<?= BASE_URL ?>index.php?page=found_items/list" class="btn btn-secondary w-100">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="col-md-6">
                    <div id="map" style="height: 400px; border-radius: 10px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize the map
    var map = L.map('map').setView([<?= $lat ?>, <?= $lng ?>], 17);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Add marker at the found item location
    var marker = L.marker([<?= $lat ?>, <?= $lng ?>]).addTo(map);
    marker.bindPopup("<b><?= htmlspecialchars($item['item_name']) ?></b><br>Found here").openPopup();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>