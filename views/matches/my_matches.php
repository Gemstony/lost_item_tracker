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
                <div class="alert alert-success"><?= $_SESSION['success'];
                unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error'];
                unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <?php if (count($matches) === 0): ?>
                <div class="alert alert-info">No matches found yet. Check back later or report more items.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Match Score</th>
                                <th>Lost Item</th>
                                <th>Found Item</th>
                                <th>Found Location</th>
                                <th>Map</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matches as $match): ?>
                                <tr id="match-row-<?= $match['id'] ?>">
                                    <td><span class="badge bg-info"><?= $match['match_score'] ?>%</span></td>
                                    <td><strong><?= htmlspecialchars($match['lost_item_name']) ?></strong><br><small><?= nl2br(htmlspecialchars(substr($match['lost_description'], 0, 100))) ?></small>
                                    </td>
                                    <td><strong><?= htmlspecialchars($match['found_item_name']) ?></strong><br><small><?= nl2br(htmlspecialchars(substr($match['found_description'], 0, 100))) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($match['found_location']) ?></td>
                                    <td>
                                        <?php if ($match['gps_latitude'] && $match['gps_longitude']): ?>
                                            <a href="<?= BASE_URL ?>index.php?page=found_items/map&id=<?= $match['found_item_id'] ?>"
                                                target="_blank" class="btn btn-sm btn-info">
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
                                            <button class="btn btn-sm btn-success confirm-match"
                                                data-id="<?= $match['id'] ?>">Confirm</button>
                                            <button class="btn btn-sm btn-danger reject-match"
                                                data-id="<?= $match['id'] ?>">Reject</button>
                                        <?php elseif ($match['status'] == 'confirmed' && ($_SESSION['role'] == 'admin' || $match['lost_user_id'] == $_SESSION['user_id'] || $match['found_user_id'] == $_SESSION['user_id'])): ?>
                                            <button class="btn btn-sm btn-primary resolve-match"
                                                data-id="<?= $match['id'] ?>">Resolve</button>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.confirm-match').forEach(btn => {
        btn.addEventListener('click', function (e) {
            let matchId = this.dataset.id;
            Swal.fire({
                title: 'Confirm Match?',
                text: "This will mark the match as confirmed. The other party will be notified.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitMatchAction(matchId, 'confirmed');
                }
            });
        });
    });

    document.querySelectorAll('.reject-match').forEach(btn => {
        btn.addEventListener('click', function (e) {
            let matchId = this.dataset.id;
            Swal.fire({
                title: 'Reject Match?',
                text: "This match will be rejected and you will not be able to undo.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, reject'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitMatchAction(matchId, 'rejected');
                }
            });
        });
    });

    document.querySelectorAll('.resolve-match').forEach(btn => {
        btn.addEventListener('click', function (e) {
            let matchId = this.dataset.id;
            Swal.fire({
                title: 'Resolve Match?',
                text: "Mark as resolved? The lost item will be marked returned and found item claimed.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, resolve'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitMatchAction(matchId, 'resolved');
                }
            });
        });
    });

    function submitMatchAction(matchId, status) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= BASE_URL ?>index.php?page=matches&action=update';
        form.style.display = 'none';
        let inputId = document.createElement('input');
        inputId.name = 'match_id';
        inputId.value = matchId;
        let inputStatus = document.createElement('input');
        inputStatus.name = 'status';
        inputStatus.value = status;
        form.appendChild(inputId);
        form.appendChild(inputStatus);
        document.body.appendChild(form);
        form.submit();
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>