<?php
$pageTitle = 'My Matches';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../models/Match.php';
require_once __DIR__ . '/../../models/Notification.php';

$matchModel = new MatchModel($pdo);
$notifModel = new NotificationModel($pdo);
$matches = $matchModel->getMatchesForUser($_SESSION['user_id']);

// Mark notifications as read when viewing matches
$unread = $notifModel->getUnread($_SESSION['user_id']);
foreach ($unread as $notif) {
    $notifModel->markAsRead($notif['id'], $_SESSION['user_id']);
}
?>

<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5><i class="fas fa-handshake"></i> My Potential Matches</h5>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <?php if (count($matches) === 0): ?>
                <div class="alert alert-info">No matches found yet. Check back later or report more items.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr><th>Match Score</th><th>Lost Item</th><th>Found Item</th><th>Found Location</th><th>Map</th><th>Status</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matches as $match): ?>
                                <tr>
                                    <td><span class="badge bg-info"><?= $match['match_score'] ?>%</span></td>
                                    <td><strong><?= htmlspecialchars($match['lost_item_name']) ?></strong><br><small><?= nl2br(htmlspecialchars(substr($match['lost_description'], 0, 100))) ?></small></td>
                                    <td><strong><?= htmlspecialchars($match['found_item_name']) ?></strong><br><small><?= nl2br(htmlspecialchars(substr($match['found_description'], 0, 100))) ?></small></td>
                                    <td><?= htmlspecialchars($match['found_location']) ?></td>
                                    <td>
                                        <?php if ($match['gps_latitude'] && $match['gps_longitude']): ?>
                                            <a href="<?= BASE_URL ?>index.php?page=found_items/map&id=<?= $match['found_item_id'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-map-marked-alt"></i> View on Map
                                            </a>
                                        <?php else: ?>—<?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $match['status'] == 'pending' ? 'warning' : 
                                            ($match['status'] == 'confirmed' ? 'primary' : 
                                            ($match['status'] == 'resolved' ? 'success' : 'secondary')) ?>">
                                            <?= ucfirst($match['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($match['status'] == 'pending'): ?>
                                            <form method="POST" action="<?= BASE_URL ?>index.php?page=matches&action=update" style="display:inline-block">
                                                <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Confirm this match?')">Confirm</button>
                                            </form>
                                            <form method="POST" action="<?= BASE_URL ?>index.php?page=matches&action=update" style="display:inline-block">
                                                <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this match?')">Reject</button>
                                            </form>
                                        <?php elseif ($match['status'] == 'confirmed' && ($_SESSION['role'] == 'admin' || $match['lost_user_id'] == $_SESSION['user_id'] || $match['found_user_id'] == $_SESSION['user_id'])): ?>
                                            <form method="POST" action="<?= BASE_URL ?>index.php?page=matches&action=update" style="display:inline-block">
                                                <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
                                                <input type="hidden" name="status" value="resolved">
                                                <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Mark as resolved? The item will be marked returned/claimed.')">Resolve</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>