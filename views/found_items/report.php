<?php
$pageTitle = 'Report Found Item';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5><i class="fas fa-smile"></i> Report Found Item</h5>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
            <form action="<?= BASE_URL ?>index.php?page=found_items/report" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Item Name *</label>
                        <input type="text" name="item_name" class="form-control" minlength="2" maxlength="100" required>
                        <div class="invalid-feedback">Enter an item name.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">Select category</option>
                            <option>Phone</option>
                            <option>Laptop</option>
                            <option>Wallet</option>
                            <option>ID Card</option>
                            <option>Keys</option>
                            <option>Bag</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" maxlength="1000"></textarea>
                    </div>
                    
                    <!-- GPS Section -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">GPS Location (where item was found)</label>
                        <div>
                            <button type="button" class="btn btn-info" onclick="getCurrentLocation()">
                                <i class="fas fa-map-marker-alt"></i> Get Current Location
                            </button>
                            <span id="locationStatus" class="ms-2"></span>
                        </div>
                        <input type="hidden" id="gps_latitude" name="gps_latitude">
                        <input type="hidden" id="gps_longitude" name="gps_longitude">
                        <small class="text-muted">Click the button to automatically capture your current GPS coordinates. This helps the owner navigate to the found location.</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Found Location (Address) *</label>
                        <input type="text" id="found_location" name="found_location" class="form-control" minlength="2" maxlength="255" required>
                        <div class="invalid-feedback">Enter where the item was found.</div>
                        <small class="text-muted">Will be auto-filled if you use GPS, but you can edit manually.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Found Date *</label>
                        <input type="date" name="found_date" class="form-control" min="2025-01-01" max="<?= date('Y-m-d') ?>" required>
                        <div class="invalid-feedback">Enter a valid found date.</div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Photo (optional)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Upload a picture of the found item (max 5MB)</small>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Submit Found Report</button>
                <a href="<?= BASE_URL ?>index.php?page=dashboard" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>assets/js/gps.js"></script>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
