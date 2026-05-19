<?php
// models/LostItem.php
class LostItem {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function create($userId, $data, $imagePath = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO lost_items (user_id, item_name, description, category, lost_location, lost_date, image_path, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        return $stmt->execute([
            $userId,
            $data['item_name'],
            $data['description'],
            $data['category'],
            $data['lost_location'],
            $data['lost_date'],
            $imagePath
        ]);
    }
    
    public function getByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM lost_items WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT l.*, u.fullname, u.email FROM lost_items l JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC");
        return $stmt->fetchAll();
    }
}
?>