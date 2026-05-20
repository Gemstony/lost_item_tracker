<?php
// models/Incident.php
class IncidentModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function create($userId, $data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO incidents (user_id, title, description, incident_type, location, incident_date, status)
            VALUES (?, ?, ?, ?, ?, ?, 'reported')
        ");
        return $stmt->execute([
            $userId,
            $data['title'],
            $data['description'],
            $data['incident_type'],
            $data['location'],
            $data['incident_date']
        ]);
    }
    
    public function getByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM incidents WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT i.*, u.fullname, u.email 
            FROM incidents i 
            JOIN users u ON i.user_id = u.id 
            ORDER BY i.created_at DESC
        ");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT i.*, u.fullname, u.email 
            FROM incidents i 
            JOIN users u ON i.user_id = u.id 
            WHERE i.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function updateStatus($id, $status, $resolutionNotes = null, $updatedBy) {
        $stmt = $this->pdo->prepare("UPDATE incidents SET status = ?, resolution_notes = ? WHERE id = ?");
        $updated = $stmt->execute([$status, $resolutionNotes, $id]);
        
        if ($updated) {
            // Log update in incident_updates
            $stmt2 = $this->pdo->prepare("
                INSERT INTO incident_updates (incident_id, status, comment, updated_by) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt2->execute([$id, $status, $resolutionNotes, $updatedBy]);
        }
        return $updated;
    }
    
    public function getUpdates($incidentId) {
        $stmt = $this->pdo->prepare("
            SELECT u.*, admin.fullname as updated_by_name 
            FROM incident_updates u 
            JOIN users admin ON u.updated_by = admin.id 
            WHERE u.incident_id = ? 
            ORDER BY u.created_at ASC
        ");
        $stmt->execute([$incidentId]);
        return $stmt->fetchAll();
    }
}
?>