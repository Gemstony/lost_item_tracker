<?php
// models/LostItem.php
class LostItem
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($userId, $data, $imagePath = null, $gpsLat = null, $gpsLng = null)
    {
        $stmt = $this->pdo->prepare("
        INSERT INTO lost_items (user_id, item_name, description, category, lost_location, gps_latitude, gps_longitude, lost_date, image_path, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
        return $stmt->execute([
            $userId,
            $data['item_name'],
            $data['description'],
            $data['category'],
            $data['lost_location'],
            $gpsLat,
            $gpsLng,
            $data['lost_date'],
            $imagePath
        ]);
    }

    public function getByUser($userId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM lost_items WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT l.*, u.fullname, u.email FROM lost_items l JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC");
        return $stmt->fetchAll();
    }

    public function getUnmatchedPending()
    {
        $stmt = $this->pdo->query("
        SELECT l.* FROM lost_items l 
        WHERE l.status = 'pending' 
        AND NOT EXISTS (
            SELECT 1 FROM matches m WHERE m.lost_item_id = l.id AND m.status != 'rejected'
        )
        ORDER BY l.created_at ASC
    ");
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT l.*, u.fullname, u.email FROM lost_items l JOIN users u ON l.user_id = u.id WHERE l.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
?>