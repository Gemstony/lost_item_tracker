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
$action = $_GET['action'] ?? 'view';
if ($action === 'run') {
    // Only admin can run matching manually
    if (!isAdmin()) redirect('dashboard');
    
    $newMatches = 0;
    $lostItems = $lostModel->getUnmatchedPending();
    $foundItems = $foundModel->getUnmatchedPending();
    
    foreach ($lostItems as $lost) {
        foreach ($foundItems as $found) {
            $score = 0;
            
            if (!empty($lost['category']) && !empty($found['category']) && strtolower($lost['category']) == strtolower($found['category'])) {
                $score += 30;
            }
            
            $lostWords = explode(' ', strtolower($lost['item_name']));
            $foundWords = explode(' ', strtolower($found['item_name']));
            $common = array_intersect($lostWords, $foundWords);
            $score += count($common) * 15;
            
            similar_text(strtolower($lost['description']), strtolower($found['description']), $descPercent);
            if ($descPercent > 40) $score += 20;
            
            if ($score >= 30) {
                $matchCreated = $matchModel->create($lost['id'], $found['id'], min($score, 100));
                if ($matchCreated) {
                    $newMatches++;
                    
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
    
    if ($newMatches > 0) {
        $_SESSION['matching_success'] = "Matching completed. Found $newMatches new potential match(es).";
    } else {
        $_SESSION['matching_success'] = "Matching completed. No new matches found.";
    }
    
    redirect('index.php?page=admin/dashboard');
}
else {
    // Show matches view
    require_once __DIR__ . '/../views/matches/my_matches.php';
}
?>