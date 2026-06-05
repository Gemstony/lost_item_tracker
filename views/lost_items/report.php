<?php
$pageTitle = 'Report Lost Item';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5><i class="fas fa-frown"></i> Report Lost Item</h5>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
            <form action="<?= BASE_URL ?>index.php?page=lost_items/report" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Item Name *</label>
                        <input type="text" name="item_name" class="form-control" required>
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
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <!-- GPS Section (exactly as found item) -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">GPS Location (where item was lost)</label>
                        <div>
                            <button type="button" class="btn btn-info" onclick="getCurrentLocation()">
                                <i class="fas fa-map-marker-alt"></i> Get Current Location
                            </button>
                            <span id="locationStatus" class="ms-2"></span>
                        </div>
                        <input type="hidden" id="gps_latitude" name="gps_latitude">
                        <input type="hidden" id="gps_longitude" name="gps_longitude">
                        <small class="text-muted">Click the button to capture your current location where you lost the item.</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lost Location (Address) *</label>
                        <input type="text" id="lost_location" name="lost_location" class="form-control" required>
                        <small class="text-muted">Will be auto-filled if you use GPS.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lost Date *</label>
                        <input type="date" name="lost_date" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Photo (optional)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Upload a picture of the lost item (max 5MB)</small>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit Report</button>
                <a href="<?= BASE_URL ?>index.php?page=dashboard" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>assets/js/gps.js"></script>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>