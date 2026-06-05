<?php
$pageTitle = 'All Matches';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../includes/helpers.php';

$page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$status = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$where = [];
$params = [];
if ($status) {
    $where[] = "m.status = ?";
    $params[] = $status;
}
if ($search) {
    $where[] = "(l.item_name LIKE ? OR f.item_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($start_date) {
    $where[] = "DATE(m.created_at) >= ?";
    $params[] = $start_date;
}
if ($end_date) {
    $where[] = "DATE(m.created_at) <= ?";
    $params[] = $end_date;
}
$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

$countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM matches m JOIN lost_items l ON m.lost_item_id = l.id JOIN found_items f ON m.found_item_id = f.id $whereSQL");
$countStmt->execute($params);
$totalRecords = $countStmt->fetch()['total'];

$query = "SELECT m.*, l.item_name as lost_item_name, l.user_id as lost_user_id, l.status as lost_status,
                 f.item_name as found_item_name, f.user_id as found_user_id, f.status as found_status,
                 f.gps_latitude, f.gps_longitude, f.found_location,
                 u1.fullname as lost_reporter, u2.fullname as found_reporter
          FROM matches m
          JOIN lost_items l ON m.lost_item_id = l.id
          JOIN found_items f ON m.found_item_id = f.id
          JOIN users u1 ON l.user_id = u1.id
          JOIN users u2 ON f.user_id = u2.id
          $whereSQL
          ORDER BY m.created_at DESC
          LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$matches = $stmt->fetchAll();

$baseUrl = BASE_URL . "index.php?page=admin/all_matches&status=$status&search=" . urlencode($search) . "&start_date=$start_date&end_date=$end_date";
?>

<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h5>All Matches</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="<?= BASE_URL ?>index.php" class="row g-3 mb-4">
                <input type="hidden" name="page" value="admin/all_matches">
                <div class="col-md-3"><input type="text" name="search" class="form-control"
                        placeholder="Search item names" value="<?= htmlspecialchars($search) ?>"></div>
                <div class="col-md-2"><select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option <?= $status == 'pending' ? 'selected' : '' ?>>pending</option>
                        <option <?= $status == 'confirmed' ? 'selected' : '' ?>>confirmed</option>
                        <option <?= $status == 'resolved' ? 'selected' : '' ?>>resolved</option>
                    </select></div>
                <div class="col-md-2"><input type="date" name="start_date" class="form-control"
                        value="<?= $start_date ?>"></div>
                <div class="col-md-2"><input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                </div>
                <div class="col-md-1"><button type="submit" class="btn btn-primary">Filter</button></div>
            </form>
            <?php if (count($matches) === 0): ?>
                <div class="alert alert-info">No matches found.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Score</th>
                                <th>Lost Item</th>
                                <th>Found Item</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Lost Reporter</th>
                                <th>Found Reporter</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matches as $m): ?>
                                <tr>
                                    <td><?= $m['id'] ?></td>
                                    <td><span class="badge bg-info"><?= $m['match_score'] ?>%</span></td>
                                    <td><?= htmlspecialchars($m['lost_item_name']) ?> (<?= $m['lost_status'] ?>)</td>
                                    <td><?= htmlspecialchars($m['found_item_name']) ?> (<?= $m['found_status'] ?>)</td>
                                    <td><?= htmlspecialchars($m['found_location']) ?></td>
                                    <td><span
                                            class="badge bg-<?= $m['status'] == 'pending' ? 'warning' : ($m['status'] == 'confirmed' ? 'primary' : 'success') ?>"><?= ucfirst($m['status']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($m['lost_reporter']) ?></td>
                                    <td><?= htmlspecialchars($m['found_reporter']) ?></td>
                                    <td><?= $m['created_at'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?= paginate($page, $totalRecords, $limit, $baseUrl) ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>