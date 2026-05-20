<?php
// controllers/ReportController.php
require_once __DIR__ . '/../includes/config.php';

if (!isAdmin()) redirect('dashboard');

$type = $_GET['type'] ?? 'lost';

// Require dompdf autoloader (adjust path as needed)
require_once __DIR__ . '/../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);

// Generate HTML content based on report type
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Report - <?= ucfirst($type) ?> Items</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #0d6efd; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #0d6efd; color: white; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <h1>Digital Tracking & Reporting System</h1>
    <h2 style="text-align:center"><?= ucfirst($type) ?> Items Report</h2>
    <p>Generated on: <?= date('Y-m-d H:i:s') ?></p>

<?php if ($type == 'lost'): ?>
    <?php
    $stmt = $pdo->query("SELECT l.*, u.fullname, u.email FROM lost_items l JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC");
    $items = $stmt->fetchAll();
    ?>
    <table>
        <thead>
            <tr><th>ID</th><th>Item</th><th>Category</th><th>Location</th><th>Lost Date</th><th>Status</th><th>Reported By</th></tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= $item['id'] ?></td>
                <td><?= htmlspecialchars($item['item_name']) ?></td>
                <td><?= htmlspecialchars($item['category']) ?></td>
                <td><?= htmlspecialchars($item['lost_location']) ?></td>
                <td><?= $item['lost_date'] ?></td>
                <td><?= ucfirst($item['status']) ?></td>
                <td><?= htmlspecialchars($item['fullname']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php elseif ($type == 'found'): ?>
    <?php
    $stmt = $pdo->query("SELECT f.*, u.fullname, u.email FROM found_items f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC");
    $items = $stmt->fetchAll();
    ?>
    <table>
        <thead><tr><th>ID</th><th>Item</th><th>Category</th><th>Location</th><th>Found Date</th><th>GPS</th><th>Status</th><th>Reported By</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= $item['id'] ?></td>
                <td><?= htmlspecialchars($item['item_name']) ?></td>
                <td><?= htmlspecialchars($item['category']) ?></td>
                <td><?= htmlspecialchars($item['found_location']) ?></td>
                <td><?= $item['found_date'] ?></td>
                <td><?= $item['gps_latitude'] ? $item['gps_latitude'].','.$item['gps_longitude'] : 'N/A' ?></td>
                <td><?= ucfirst($item['status']) ?></td>
                <td><?= htmlspecialchars($item['fullname']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php elseif ($type == 'incidents'): ?>
    <?php
    $stmt = $pdo->query("SELECT i.*, u.fullname FROM incidents i JOIN users u ON i.user_id = u.id ORDER BY i.created_at DESC");
    $items = $stmt->fetchAll();
    ?>
    <table>
        <thead><tr><th>ID</th><th>Title</th><th>Type</th><th>Location</th><th>Incident Date</th><th>Status</th><th>Reported By</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= $item['id'] ?></td>
                <td><?= htmlspecialchars($item['title']) ?></td>
                <td><?= ucfirst($item['incident_type']) ?></td>
                <td><?= htmlspecialchars($item['location']) ?></td>
                <td><?= $item['incident_date'] ?></td>
                <td><?= ucfirst($item['status']) ?></td>
                <td><?= htmlspecialchars($item['fullname']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
    <div class="footer">National Institute of Transport (NIT) - Lost & Found Tracking System</div>
</body>
</html>
<?php
$html = ob_get_clean();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("report_$type.pdf", array("Attachment" => false));
?>