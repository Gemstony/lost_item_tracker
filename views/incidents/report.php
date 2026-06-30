<?php
$pageTitle = 'Report Incident';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <h5><i class="fas fa-exclamation-triangle"></i> Report an Incident</h5>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <form action="<?= BASE_URL ?>index.php?page=incidents&action=create" method="POST" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" minlength="2" maxlength="100" required>
                        <div class="invalid-feedback">Enter an incident title.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Incident Type *</label>
                        <select name="incident_type" class="form-select" required>
                            <option value="theft">Theft</option>
                            <option value="safety">Safety Hazard</option>
                            <option value="misconduct">Misconduct</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Incident *</label>
                        <input type="date" name="incident_date" class="form-control" max="<?= date('Y-m-d') ?>" required>
                        <div class="invalid-feedback">Enter a valid incident date.</div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Location *</label>
                        <input type="text" name="location" class="form-control" placeholder="Building, room, or area" minlength="2" maxlength="255" required>
                        <div class="invalid-feedback">Enter the incident location.</div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="5" minlength="10" maxlength="1000" required></textarea>
                        <div class="invalid-feedback">Enter a description of at least 10 characters.</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-danger">Submit Incident</button>
                <a href="<?= BASE_URL ?>index.php?page=dashboard" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
