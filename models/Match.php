<?php
// models/Match.php
class MatchModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function create($lostItemId, $foundItemId, $score) {
        // Check if match already exists
        $stmt = $this->pdo->prepare("SELECT id FROM matches WHERE lost_item_id = ? AND found_item_id = ?");
        $stmt->execute([$lostItemId, $foundItemId]);
        if ($stmt->fetch()) return false;
        
        $stmt = $this->pdo->prepare("
            INSERT INTO matches (lost_item_id, found_item_id, match_score, status, created_at)
            VALUES (?, ?, ?, 'pending', NOW())
        ");
        return $stmt->execute([$lostItemId, $foundItemId, $score]);
    }
    
    public function getMatchesForUser($userId) {
        $stmt = $this->pdo->prepare("
            SELECT m.*, 
                   l.item_name as lost_item_name, l.description as lost_description, l.user_id as lost_user_id,
                   f.item_name as found_item_name, f.description as found_description, f.user_id as found_user_id,
                   f.gps_latitude, f.gps_longitude, f.found_location
            FROM matches m
            JOIN lost_items l ON m.lost_item_id = l.id
            JOIN found_items f ON m.found_item_id = f.id
            WHERE l.user_id = ? OR f.user_id = ?
            ORDER BY m.created_at DESC
        ");
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll();
    }
    
    public function updateStatus($matchId, $status, $userId) {
        $stmt = $this->pdo->prepare("
            UPDATE matches SET status = ?, resolved_at = NOW() WHERE id = ? AND status = 'pending'
        ");
        $updated = $stmt->execute([$status, $matchId]);
        
        if ($updated && $status == 'resolved') {
            // Also update the lost and found items status
            $match = $this->getById($matchId);
            if ($match) {
                $this->pdo->prepare("UPDATE lost_items SET status = 'returned' WHERE id = ?")->execute([$match['lost_item_id']]);
                $this->pdo->prepare("UPDATE found_items SET status = 'claimed' WHERE id = ?")->execute([$match['found_item_id']]);
            }
        }
        return $updated;
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM matches WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
?>