<?php
$pageTitle = 'All Found Items';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../includes/helpers.php';

$page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$status = $_GET['status'] ?? '';
$category = $_GET['category'] ?? '';
$search = trim($_GET['search'] ?? '');
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$where = [];
$params = [];
if ($status) {
    $where[] = "f.status = ?";
    $params[] = $status;
}
if ($category) {
    $where[] = "f.category = ?";
    $params[] = $category;
}
if ($search) {
    $where[] = "(f.item_name LIKE ? OR f.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($start_date) {
    $where[] = "DATE(f.created_at) >= ?";
    $params[] = $start_date;
}
if ($end_date) {
    $where[] = "DATE(f.created_at) <= ?";
    $params[] = $end_date;
}
$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

$countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM found_items f $whereSQL");
$countStmt->execute($params);
$totalRecords = $countStmt->fetch()['total'];

$query = "SELECT f.*, u.fullname, u.email FROM found_items f JOIN users u ON f.user_id = u.id $whereSQL ORDER BY f.created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$items = $stmt->fetchAll();

$catStmt = $pdo->query("SELECT DISTINCT category FROM found_items WHERE category IS NOT NULL AND category != ''");
$categories = $catStmt->fetchAll();
$baseUrl = BASE_URL . "index.php?page=admin/all_found_items&status=$status&category=$category&search=" . urlencode($search) . "&start_date=$start_date&end_date=$end_date";
?>

<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5>All Found Items</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="<?= BASE_URL ?>index.php" class="row g-3 mb-4">
                <input type="hidden" name="page" value="admin/all_found_items">
                <div class="col-md-3"><input type="text" name="search" class="form-control" placeholder="Search..."
                        value="<?= htmlspecialchars($search) ?>"></div>
                <div class="col-md-2"><select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option <?= $status == 'pending' ? 'selected' : '' ?>>pending</option>
                        <option <?= $status == 'claimed' ? 'selected' : '' ?>>claimed</option>
                    </select></div>
                <div class="col-md-2"><select name="category" class="form-select">
                        <option value="">All Categories</option><?php foreach ($categories as $c): ?>
                            <option value="<?= $c['category'] ?>" <?= $category == $c['category'] ? 'selected' : '' ?>>
                                <?= $c['category'] ?></option><?php endforeach; ?>
                    </select></div>
                <div class="col-md-2"><input type="date" name="start_date" class="form-control"
                        value="<?= $start_date ?>"></div>
                <div class="col-md-2"><input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                </div>
                <div class="col-md-1"><button type="submit" class="btn btn-primary">Filter</button></div>
            </form>
            <?php if (count($items) === 0): ?>
                <div class="alert alert-info">No found items.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Found Date</th>
                                <th>Status</th>
                                <th>Reported By</th>
                                <th>GPS</th>
                                <th>Image</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= $item['id'] ?></td>
                                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                                    <td><?= $item['category'] ?></td>
                                    <td><?= htmlspecialchars($item['found_location']) ?></td>
                                    <td><?= $item['found_date'] ?></td>
                                    <td><span
                                            class="badge bg-<?= $item['status'] == 'pending' ? 'warning' : 'success' ?>"><?= ucfirst($item['status']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($item['fullname']) ?></td>
                                    <td><?php if ($item['gps_latitude']): ?><a
                                                href="<?= BASE_URL ?>index.php?page=found_items/map&id=<?= $item['id'] ?>"
                                                target="_blank">Map</a><?php else: ?>—<?php endif; ?></td>
                                    <td><?php if ($item['image_path']): ?><a href="<?= BASE_URL . $item['image_path'] ?>"
                                                target="_blank">View</a><?php else: ?>—<?php endif; ?></td>
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