<?php
$pageTitle = 'Admin Reports';
require_once __DIR__ . '/../layouts/header.php';

// Get date filters
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-t');
$statusFilter = $_GET['status'] ?? 'all';
$categoryFilter = $_GET['category'] ?? 'all';

// Base conditions
$dateCondition = "DATE(created_at) BETWEEN '$startDate' AND '$endDate'";

// Summary statistics
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM lost_items WHERE $dateCondition");
$totalLost = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM found_items WHERE $dateCondition");
$totalFound = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM incidents WHERE $dateCondition");
$totalIncidents = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM matches");
$totalMatches = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM matches WHERE status = 'resolved'");
$resolvedMatches = $stmt->fetch()['total'];

// Lost items by category (for chart)
$stmt = $pdo->query("
    SELECT category, COUNT(*) as count FROM lost_items 
    WHERE category IS NOT NULL AND category != '' 
    GROUP BY category ORDER BY count DESC LIMIT 5
");
$lostCategories = $stmt->fetchAll();

// Incidents by type
$stmt = $pdo->query("
    SELECT incident_type, COUNT(*) as count FROM incidents 
    WHERE $dateCondition
    GROUP BY incident_type
");
$incidentTypes = $stmt->fetchAll();

// Monthly trends (last 6 months)
$stmt = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
           COUNT(*) as lost_count
    FROM lost_items 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY month ORDER BY month ASC
");
$monthlyLost = $stmt->fetchAll();

$stmt = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
           COUNT(*) as found_count
    FROM found_items 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY month ORDER BY month ASC
");
$monthlyFound = $stmt->fetchAll();

// Build lost items table with filters
$lostQuery = "SELECT l.*, u.fullname FROM lost_items l 
              JOIN users u ON l.user_id = u.id 
              WHERE DATE(l.created_at) BETWEEN '$startDate' AND '$endDate'";
if ($statusFilter !== 'all') {
    $lostQuery .= " AND l.status = '$statusFilter'";
}
if ($categoryFilter !== 'all') {
    $lostQuery .= " AND l.category = '$categoryFilter'";
}
$lostQuery .= " ORDER BY l.created_at DESC LIMIT 100";
$lostItems = $pdo->query($lostQuery)->fetchAll();

// Found items table
$foundQuery = "SELECT f.*, u.fullname FROM found_items f 
               JOIN users u ON f.user_id = u.id 
               WHERE DATE(f.created_at) BETWEEN '$startDate' AND '$endDate'";
if ($statusFilter !== 'all') {
    $foundQuery .= " AND f.status = '$statusFilter'";
}
if ($categoryFilter !== 'all') {
    $foundQuery .= " AND f.category = '$categoryFilter'";
}
$foundQuery .= " ORDER BY f.created_at DESC LIMIT 100";
$foundItems = $pdo->query($foundQuery)->fetchAll();

// Incidents table
$incidentQuery = "SELECT i.*, u.fullname FROM incidents i 
                  JOIN users u ON i.user_id = u.id 
                  WHERE DATE(i.created_at) BETWEEN '$startDate' AND '$endDate'";
if ($statusFilter !== 'all') {
    $incidentQuery .= " AND i.status = '$statusFilter'";
}
$incidentQuery .= " ORDER BY i.created_at DESC LIMIT 100";
$incidents = $pdo->query($incidentQuery)->fetchAll();

// Get unique categories for filter dropdown
$categories = $pdo->query("SELECT DISTINCT category FROM lost_items WHERE category IS NOT NULL AND category != ''")->fetchAll();
?>

<div class="container-fluid">
    <!-- Filter Form -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5><i class="fas fa-filter"></i> Filter Reports</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="<?= BASE_URL ?>index.php" class="row g-3">
                <input type="hidden" name="page" value="admin/reports">
                <div class="col-md-3">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $startDate ?>">
                </div>
                <div class="col-md-3">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $endDate ?>">
                </div>
                <div class="col-md-2">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="all" <?= $statusFilter == 'all' ? 'selected' : '' ?>>All</option>
                        <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="returned" <?= $statusFilter == 'returned' ? 'selected' : '' ?>>Returned/Resolved
                        </option>
                        <option value="claimed" <?= $statusFilter == 'claimed' ? 'selected' : '' ?>>Claimed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Category</label>
                    <select name="category" class="form-select">
                        <option value="all" <?= $categoryFilter == 'all' ? 'selected' : '' ?>>All</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['category'] ?>" <?= $categoryFilter == $cat['category'] ? 'selected' : '' ?>>
                                <?= $cat['category'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5><?= $totalUsers ?></h5><small>Users</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5><?= $totalLost ?></h5><small>Lost Items</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5><?= $totalFound ?></h5><small>Found Items</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5><?= $totalIncidents ?></h5><small>Incidents</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5><?= $totalMatches ?></h5><small>Total Matches</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-secondary">
                <div class="card-body">
                    <h5><?= $resolvedMatches ?></h5><small>Resolved</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">Lost Items by Category</div>
                <div class="card-body"><canvas id="lostCategoryChart" height="200"></canvas></div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">Incidents by Type</div>
                <div class="card-body"><canvas id="incidentTypeChart" height="200"></canvas></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">Monthly Trends (Lost vs Found)</div>
                <div class="card-body"><canvas id="trendChart" height="100"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Lost Items Table with Export -->
    <div class="card shadow mb-4">
        <div class="card-header bg-warning text-dark">
            <h5>
                Lost Items Report
                <div class="float-end d-flex gap-2">
                    <button class="btn btn-sm btn-success"
                        onclick="exportTableToCSV('lost_items.csv', '#lostItemsTable')">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </button>
                    <a href="<?= BASE_URL ?>index.php?page=user/reports/pdf&type=lost"
                        class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped" id="lostItemsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Date Lost</th>
                        <th>Status</th>
                        <th>Reported By</th>
                        <th>Reported On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lostItems as $item): ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                            <td><?= htmlspecialchars($item['category']) ?></td>
                            <td><?= htmlspecialchars($item['lost_location']) ?></td>
                            <td><?= $item['lost_date'] ?></td>
                            <td><?= ucfirst($item['status']) ?></td>
                            <td><?= htmlspecialchars($item['fullname']) ?></td>
                            <td><?= $item['created_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Found Items Table -->
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            <h5>Found Items Report
                <div class="float-end d-flex gap-2">
                    <button class="btn btn-sm btn-success"
                        onclick="exportTableToCSV('found_items.csv', '#foundItemsTable')">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </button>
                    <a href="<?= BASE_URL ?>index.php?page=user/reports/pdf&type=found"
                        class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped" id="foundItemsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>GPS</th>
                        <th>Date Found</th>
                        <th>Status</th>
                        <th>Reported By</th>
                        <th>Reported On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($foundItems as $item): ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                            <td><?= htmlspecialchars($item['category']) ?></td>
                            <td><?= htmlspecialchars($item['found_location']) ?></td>
                            <td><?= $item['gps_latitude'] ? $item['gps_latitude'] . ',' . $item['gps_longitude'] : 'N/A' ?>
                            </td>
                            <td><?= $item['found_date'] ?></td>
                            <td><?= ucfirst($item['status']) ?></td>
                            <td><?= htmlspecialchars($item['fullname']) ?></td>
                            <td><?= $item['created_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Incidents Table -->
    <div class="card shadow mb-4">
        <div class="card-header bg-danger text-white">
            <h5>Incidents Report

                <div class="float-end d-flex gap-2">
                    <button class="btn btn-sm btn-success"
                        onclick="exportTableToCSV('incidents.csv', '#incidentsTable')">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </button>
                    <a href="<?= BASE_URL ?>index.php?page=user/reports/pdf&type=incidents"
                        class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>

            </h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped" id="incidentsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Reported By</th>
                        <th>Reported On</th>
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
                            <td><?= ucfirst($inc['status']) ?></td>
                            <td><?= htmlspecialchars($inc['fullname']) ?></td>
                            <td><?= $inc['created_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js and export script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Lost category chart
    new Chart(document.getElementById('lostCategoryChart'), {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_column($lostCategories, 'category')) ?>,
            datasets: [{ data: <?= json_encode(array_column($lostCategories, 'count')) ?>, backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8'] }]
        }
    });
    // Incident type chart
    new Chart(document.getElementById('incidentTypeChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($incidentTypes, 'incident_type')) ?>,
            datasets: [{ label: 'Count', data: <?= json_encode(array_column($incidentTypes, 'count')) ?>, backgroundColor: '#dc3545' }]
        }
    });
    // Monthly trend chart (combine lost and found)
    const monthsLost = <?= json_encode(array_column($monthlyLost, 'month')) ?>;
    const lostCounts = <?= json_encode(array_column($monthlyLost, 'lost_count')) ?>;
    // Build found data matching months
    const foundMap = <?php
    $foundMap = [];
    foreach ($monthlyFound as $f) {
        $foundMap[$f['month']] = $f['found_count'];
    }
    echo json_encode($foundMap);
    ?>;
    const foundCounts = monthsLost.map(m => foundMap[m] || 0);
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: monthsLost,
            datasets: [
                { label: 'Lost Items', data: lostCounts, borderColor: '#ffc107', fill: false },
                { label: 'Found Items', data: foundCounts, borderColor: '#28a745', fill: false }
            ]
        }
    });

    // Export table to CSV
    function exportTableToCSV(filename, tableSelector) {
        let csv = [];
        let rows = document.querySelectorAll(tableSelector + ' tr');
        for (let row of rows) {
            let rowData = [];
            for (let cell of row.querySelectorAll('th, td')) {
                rowData.push('"' + cell.innerText.replace(/"/g, '""') + '"');
            }
            csv.push(rowData.join(','));
        }
        let blob = new Blob([csv.join('\n')], { type: 'text/csv' });
        let link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>