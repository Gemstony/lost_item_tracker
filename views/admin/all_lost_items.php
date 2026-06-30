<?php
$pageTitle = 'All Lost Items';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Pagination & filters
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max(1, $page); // ensure at least 1
$limit = 10;
$offset = ($page - 1) * $limit;

$status = isset($_GET['status']) ? $_GET['status'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Build WHERE clause
$where = [];
$params = [];
if ($status) {
    $where[] = "l.status = ?";
    $params[] = $status;
}
if ($category) {
    $where[] = "l.category = ?";
    $params[] = $category;
}
if ($search) {
    $where[] = "(l.item_name LIKE ? OR l.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($start_date) {
    $where[] = "DATE(l.created_at) >= ?";
    $params[] = $start_date;
}
if ($end_date) {
    $where[] = "DATE(l.created_at) <= ?";
    $params[] = $end_date;
}
$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

// Get total count for pagination
$countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM lost_items l $whereSQL");
$countStmt->execute($params);
$totalRecords = $countStmt->fetch()['total'];
$totalPages = ceil($totalRecords / $limit);

// Adjust page if beyond total pages
if ($page > $totalPages && $totalPages > 0) {
    $page = $totalPages;
    $offset = ($page - 1) * $limit;
}

// Fetch data with limit – use string concatenation for LIMIT after validating integers
$limit = (int) $limit;
$offset = (int) $offset;
$query = "SELECT l.*, u.fullname, u.email 
          FROM lost_items l 
          JOIN users u ON l.user_id = u.id 
          $whereSQL 
          ORDER BY l.created_at DESC 
          LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$items = $stmt->fetchAll();

// Get categories for filter dropdown
$catStmt = $pdo->query("SELECT DISTINCT category FROM lost_items WHERE category IS NOT NULL AND category != ''");
$categories = $catStmt->fetchAll();

// Build base URL for pagination
$baseUrl = BASE_URL . "index.php?page=admin/all_lost_items&status=" . urlencode($status) . "&category=" . urlencode($category) . "&search=" . urlencode($search) . "&start_date=" . urlencode($start_date) . "&end_date=" . urlencode($end_date);
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5><i class="fas fa-frown"></i> All Lost Items (All Users)</h5>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" action="<?= BASE_URL ?>index.php" class="row g-3 mb-4">
                <input type="hidden" name="page" value="admin/all_lost_items">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search item or description"
                        value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="returned" <?= $status == 'returned' ? 'selected' : '' ?>>Returned</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['category']) ?>"
                                <?= $category == $cat['category'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['category']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="start_date" class="form-control" placeholder="Start Date"
                        value="<?= $start_date ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" name="end_date" class="form-control" placeholder="End Date"
                        value="<?= $end_date ?>">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>

            <?php if (count($items) === 0): ?>
                <div class="alert alert-info">No lost items found.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Lost Date</th>
                                <th>Status</th>
                                <th>Reported By</th>
                                <th>Reported On</th>
                                <th>GPS</th>
                                <th>Image</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= $item['id'] ?></td>
                                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                                    <td><?= htmlspecialchars($item['category']) ?></td>
                                    <td><?= htmlspecialchars($item['lost_location']) ?></td>
                                    <td><?= $item['lost_date'] ?></td>
                                    <td><span
                                            class="badge bg-<?= $item['status'] == 'pending' ? 'warning' : 'success' ?>"><?= ucfirst($item['status']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($item['fullname']) ?> (<?= $item['email'] ?>)</td>
                                    <td><?= $item['created_at'] ?></td>
                                    <td>
                                        <?php if ($item['gps_latitude'] && $item['gps_longitude']): ?>
                                            <a class="btn btn-sm btn-info" href="<?= BASE_URL ?>index.php?page=lost_items/map&id=<?= $item['id'] ?>"
                                                >
                                                <i class="fas fa-map-marked-alt"></i> Map
                                            </a>
                                        <?php else: ?>—<?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item['image_path']): ?>
                                            <a class="btn btn-sm btn-primary" href="<?= BASE_URL . $item['image_path'] ?>" target="_blank">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        <?php else: ?>—<?php endif; ?>
                                    </td>
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