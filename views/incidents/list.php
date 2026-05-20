<?php
$pageTitle = 'Incidents';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../models/Incident.php';

$incidentModel = new IncidentModel($pdo);

if (isAdmin()) {
    $incidents = $incidentModel->getAll();
} else {
    $incidents = $incidentModel->getByUser($_SESSION['user_id']);
}
?>

<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <h5><i class="fas fa-list"></i> <?= isAdmin() ? 'All Incidents' : 'My Incidents' ?></h5>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <?php if (count($incidents) === 0): ?>
                <div class="alert alert-info">No incidents reported yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Reported By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($incidents as $inc): ?>
                                <tr>
                                    <td><?= $inc['id'] ?></td>
                                    <td><?= htmlspecialchars($inc['title']) ?></td>
                                    <td><?= ucfirst($inc['incident_type']) ?></td>
                                    <td><?= htmlspecialchars($inc['location']) ?></td>
                                    <td><?= $inc['incident_date'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $inc['status'] == 'reported' ? 'warning' : 
                                            ($inc['status'] == 'investigating' ? 'info' : 
                                            ($inc['status'] == 'resolved' ? 'success' : 'secondary')) ?>">
                                            <?= ucfirst($inc['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($inc['fullname'] ?? $inc['user_id']) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modal<?= $inc['id'] ?>">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Modal for details -->
                                <div class="modal fade" id="modal<?= $inc['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5>Incident Details: <?= htmlspecialchars($inc['title']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($inc['description'])) ?></p>
                                                <p><strong>Location:</strong> <?= htmlspecialchars($inc['location']) ?></p>
                                                <p><strong>Date:</strong> <?= $inc['incident_date'] ?></p>
                                                <p><strong>Status:</strong> <?= ucfirst($inc['status']) ?></p>
                                                <?php if ($inc['resolution_notes']): ?>
                                                    <p><strong>Resolution Notes:</strong><br><?= nl2br(htmlspecialchars($inc['resolution_notes'])) ?></p>
                                                <?php endif; ?>
                                                
                                                <?php if (isAdmin() && $inc['status'] != 'closed'): ?>
                                                    <hr>
                                                    <h6>Update Status</h6>
                                                    <form method="POST" action="<?= BASE_URL ?>index.php?page=incidents&action=update">
                                                        <input type="hidden" name="incident_id" value="<?= $inc['id'] ?>">
                                                        <div class="mb-2">
                                                            <select name="status" class="form-select" required>
                                                                <option value="reported" <?= $inc['status']=='reported'?'selected':'' ?>>Reported</option>
                                                                <option value="investigating" <?= $inc['status']=='investigating'?'selected':'' ?>>Investigating</option>
                                                                <option value="resolved" <?= $inc['status']=='resolved'?'selected':'' ?>>Resolved</option>
                                                                <option value="closed" <?= $inc['status']=='closed'?'selected':'' ?>>Closed</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-2">
                                                            <textarea name="resolution_notes" class="form-control" rows="2" placeholder="Resolution notes (optional)"></textarea>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>