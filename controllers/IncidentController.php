<?php
// controllers/IncidentController.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../models/Incident.php';
require_once __DIR__ . '/../models/Notification.php';

$incidentModel = new IncidentModel($pdo);
$notifModel = new NotificationModel($pdo);

$action = $_GET['action'] ?? 'report';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    $userId = $_SESSION['user_id'];
    $data = [
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'incident_type' => $_POST['incident_type'],
        'location' => $_POST['location'],
        'incident_date' => $_POST['incident_date']
    ];
    
    if ($incidentModel->create($userId, $data)) {
        $_SESSION['success'] = "Incident reported successfully.";
        redirect('index.php?page=incidents/list');
    } else {
        $_SESSION['error'] = "Failed to report incident.";
        redirect('index.php?page=incidents/report');
    }
}
elseif ($action === 'update' && isAdmin()) {
    $incidentId = $_POST['incident_id'];
    $status = $_POST['status'];
    $resolutionNotes = $_POST['resolution_notes'] ?? null;
    
    if ($incidentModel->updateStatus($incidentId, $status, $resolutionNotes, $_SESSION['user_id'])) {
        // Notify the user who reported the incident
        $incident = $incidentModel->getById($incidentId);
        $notifModel->create(
            $incident['user_id'],
            'incident_update',
            "Your incident '{$incident['title']}' status has been updated to: " . ucfirst($status),
            BASE_URL . "index.php?page=incidents/list"
        );
        $_SESSION['success'] = "Incident status updated.";
    } else {
        $_SESSION['error'] = "Update failed.";
    }
    redirect('index.php?page=admin/dashboard');
}
else {
    // Show the report form
    require_once __DIR__ . '/../views/incidents/report.php';
}
?>