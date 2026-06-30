<?php
// controllers/LostItemController.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/LostItem.php';

$lostItemModel = new LostItem($pdo);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $gpsLat = !empty($_POST['gps_latitude']) ? $_POST['gps_latitude'] : null;
    $gpsLng = !empty($_POST['gps_longitude']) ? $_POST['gps_longitude'] : null;
    $errors = array_merge(
        validateItemReportForm($_POST, 'lost_location', 'lost_date'),
        validateGpsCoordinates($gpsLat, $gpsLng),
        validateImageUpload($_FILES['image'] ?? null)
    );

    if ($errors) {
        $_SESSION['error'] = implode(' ', $errors);
        redirect('index.php?page=lost_items/report');
    }

    $data = [
        'item_name' => trim($_POST['item_name']),
        'description' => trim($_POST['description'] ?? ''),
        'category' => trim($_POST['category'] ?? ''),
        'lost_location' => trim($_POST['lost_location']),
        'lost_date' => trim($_POST['lost_date'])
    ];

    // Handle image upload
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/lost_items/';
        if (!is_dir($uploadDir))
            mkdir($uploadDir, 0777, true);

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'lost_' . time() . '_' . uniqid() . '.' . $ext;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $imagePath = 'uploads/lost_items/' . $filename;
        }
    }

    if ($lostItemModel->create($userId, $data, $imagePath, $gpsLat, $gpsLng)) {
        $_SESSION['success'] = "Lost item reported successfully.";
        redirect('index.php?page=lost_items/list');
    } else {
        $_SESSION['error'] = "Failed to report lost item.";
        redirect('index.php?page=lost_items/report');
    }
} else {
    // Show the form
    require_once __DIR__ . '/../views/lost_items/report.php';
}
?>
