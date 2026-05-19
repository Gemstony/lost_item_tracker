<?php
// controllers/MatchController.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../models/LostItem.php';
require_once __DIR__ . '/../models/FoundItem.php';
require_once __DIR__ . '/../models/Match.php';
require_once __DIR__ . '/../models/Notification.php';

$matchModel = new MatchModel($pdo);
$lostModel = new LostItem($pdo);
$foundModel = new FoundItem($pdo);
$notifModel = new NotificationModel($pdo);

// Determine action
$action = $_GET['action'] ?? 'run';

if ($action === 'run') {
    // Only admin can run matching manually
    if (!isAdmin()) redirect('dashboard');
    
    $newMatches = 0;
    $lostItems = $lostModel->getUnmatchedPending();
    $foundItems = $foundModel->getUnmatchedPending();
    
    foreach ($lostItems as $lost) {
        foreach ($foundItems as $found) {
            $score = 0;
            
            // Same category? +30 points
            if (!empty($lost['category']) && !empty($found['category']) && strtolower($lost['category']) == strtolower($found['category'])) {
                $score += 30;
            }
            
            // Keyword matching in item name
            $lostWords = explode(' ', strtolower($lost['item_name']));
            $foundWords = explode(' ', strtolower($found['item_name']));
            $common = array_intersect($lostWords, $foundWords);
            $score += count($common) * 15;
            
            // Partial description match (simple)
            similar_text(strtolower($lost['description']), strtolower($found['description']), $descPercent);
            if ($descPercent > 40) $score += 20;
            
            // Only create match if score >= 30
            if ($score >= 30) {
                $matchCreated = $matchModel->create($lost['id'], $found['id'], min($score, 100));
                if ($matchCreated) {
                    $newMatches++;
                    
                    // Notify both users (lost owner and finder)
                    $notifModel->create(
                        $lost['user_id'],
                        'match',
                        "Potential match found for your lost item '{$lost['item_name']}'. A found item matches.",
                        BASE_URL . "index.php?page=matches/view"
                    );
                    $notifModel->create(
                        $found['user_id'],
                        'match',
                        "Potential match found for the item you reported as found: '{$found['item_name']}'.",
                        BASE_URL . "index.php?page=matches/view"
                    );
                }
            }
        }
    }
    
    $_SESSION['success'] = "Matching completed. Found $newMatches new potential matches.";
    redirect('index.php?page=admin/dashboard');
}
elseif ($action === 'update') {
    // User confirms or rejects a match
    $matchId = $_POST['match_id'] ?? 0;
    $status = $_POST['status'] ?? ''; // 'confirmed', 'rejected', 'resolved'
    
    if ($matchId && in_array($status, ['confirmed', 'rejected', 'resolved'])) {
        $matchModel->updateStatus($matchId, $status, $_SESSION['user_id']);
        $_SESSION['success'] = "Match $status successfully.";
    }
    redirect('index.php?page=matches/view');
}
else {
    // Show matches view
    require_once __DIR__ . '/../views/matches/my_matches.php';
}
?>