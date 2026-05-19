<?php
// controllers/FoundItemController.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../models/FoundItem.php';

$foundItemModel = new FoundItem($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    
    // Get GPS coordinates from form (sent via hidden inputs from JavaScript)
    $gpsLat = !empty($_POST['gps_latitude']) ? $_POST['gps_latitude'] : null;
    $gpsLng = !empty($_POST['gps_longitude']) ? $_POST['gps_longitude'] : null;
    
    $data = [
        'item_name' => $_POST['item_name'],
        'description' => $_POST['description'],
        'category' => $_POST['category'],
        'found_location' => $_POST['found_location'],
        'gps_latitude' => $gpsLat,
        'gps_longitude' => $gpsLng,
        'found_date' => $_POST['found_date']
    ];
    
    // Handle image upload (optional)
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/found_items/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'found_' . time() . '_' . uniqid() . '.' . $ext;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $imagePath = 'uploads/found_items/' . $filename;
        }
    }
    
    if ($foundItemModel->create($userId, $data, $imagePath)) {
        $_SESSION['success'] = "Found item reported successfully with GPS location.";
        redirect('index.php?page=found_items/list');
    } else {
        $_SESSION['error'] = "Failed to report found item.";
        redirect('index.php?page=found_items/report');
    }
} else {
    // Show the form
    require_once __DIR__ . '/../views/found_items/report.php';
}
?>