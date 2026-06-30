<?php
$pageTitle = 'My Lost Items';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../models/LostItem.php';

$lostModel = new LostItem($pdo);
$items = $lostModel->getByUser($_SESSION['user_id']);
?>

<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5><i class="fas fa-list"></i> My Reported Lost Items</h5>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success'];
                unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <?php if (count($items) === 0): ?>
                <div class="alert alert-info">You haven't reported any lost items yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Map</th>
                                <th>Image</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                                    <td><?= htmlspecialchars($item['category']) ?></td>
                                    <td><?= htmlspecialchars($item['lost_location']) ?></td>
                                    <td><?= $item['lost_date'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $item['status'] == 'pending' ? 'warning' : 'success' ?>">
                                            <?= ucfirst($item['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($item['gps_latitude']) && !empty($item['gps_longitude'])): ?>
                                            <a class="btn btn-sm btn-info" href="<?= BASE_URL ?>index.php?page=lost_items/map&id=<?= $item['id'] ?>"
                                                >
                                                <i class="fas fa-map-marked-alt"></i> View Map
                                            </a>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item['image_path']): ?>
                                            <a href="<?= BASE_URL . $item['image_path'] ?>" target="_blank">View</a>
                                        <?php else: ?>—<?php endif; ?>
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