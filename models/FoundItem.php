<?php
// models/FoundItem.php
class FoundItem {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function create($userId, $data, $imagePath = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO found_items 
            (user_id, item_name, description, category, found_location, gps_latitude, gps_longitude, found_date, image_path, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        return $stmt->execute([
            $userId,
            $data['item_name'],
            $data['description'],
            $data['category'],
            $data['found_location'],
            $data['gps_latitude'],
            $data['gps_longitude'],
            $data['found_date'],
            $imagePath
        ]);
    }
    
    public function getByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM found_items WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT f.*, u.fullname, u.email FROM found_items f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT f.*, u.fullname, u.email FROM found_items f JOIN users u ON f.user_id = u.id WHERE f.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
?>