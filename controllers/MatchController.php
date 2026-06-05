<?php
// controllers/MatchController.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../models/Match.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/LostItem.php';
require_once __DIR__ . '/../models/FoundItem.php';

$matchModel = new MatchModel($pdo);
$notifModel = new NotificationModel($pdo);
$lostModel = new LostItem($pdo);
$foundModel = new FoundItem($pdo);

$action = $_GET['action'] ?? '';

if ($action === 'run') {
    // Only admin can run matching
    if (!isAdmin())
        redirect('index.php?page=dashboard');

    $newMatches = 0;
    $lostItems = $lostModel->getUnmatchedPending();
    $foundItems = $foundModel->getUnmatchedPending();

    foreach ($lostItems as $lost) {
        foreach ($foundItems as $found) {
            $score = 0;

            // Category match
            if (!empty($lost['category']) && !empty($found['category']) && strtolower($lost['category']) == strtolower($found['category'])) {
                $score += 30;
            }

            // Keyword matching in item name
            $lostWords = explode(' ', strtolower($lost['item_name']));
            $foundWords = explode(' ', strtolower($found['item_name']));
            $common = array_intersect($lostWords, $foundWords);
            $score += count($common) * 15;

            // Description similarity
            similar_text(strtolower($lost['description']), strtolower($found['description']), $descPercent);
            if ($descPercent > 40)
                $score += 20;

            if ($score >= 30) {
                $matchCreated = $matchModel->create($lost['id'], $found['id'], min($score, 100));
                if ($matchCreated) {
                    $newMatches++;
                    // Notify both users
                    $notifModel->create(
                        $lost['user_id'],
                        'match',
                        "Potential match found for your lost item '{$lost['item_name']}'.",
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

    $_SESSION['matching_success'] = "Matching completed. Found $newMatches new potential match(es).";
    redirect('index.php?page=admin/dashboard');
} elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update match status (confirm, reject, resolve)
    $matchId = $_POST['match_id'] ?? 0;
    $status = $_POST['status'] ?? '';
    $userId = $_SESSION['user_id'];

    // Validate inputs
    if (!$matchId || !in_array($status, ['confirmed', 'rejected', 'resolved'])) {
        $_SESSION['error'] = "Invalid request.";
        redirect('index.php?page=matches/view');
    }

    // Fetch match to verify permission
    $match = $matchModel->getById($matchId);
    if (!$match) {
        $_SESSION['error'] = "Match not found.";
        redirect('index.php?page=matches/view');
    }

    // Get lost and found items to check ownership
    $stmt = $pdo->prepare("SELECT user_id FROM lost_items WHERE id = ?");
    $stmt->execute([$match['lost_item_id']]);
    $lostOwner = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT user_id FROM found_items WHERE id = ?");
    $stmt->execute([$match['found_item_id']]);
    $foundOwner = $stmt->fetchColumn();

    // Allow if user is admin, or owner of lost item, or owner of found item
    if (!isAdmin() && $userId != $lostOwner && $userId != $foundOwner) {
        $_SESSION['error'] = "You don't have permission to update this match.";
        redirect('index.php?page=matches/view');
    }

    // Update match status
    $updated = $matchModel->updateStatus($matchId, $status, $userId);

    if ($updated) {
        $_SESSION['success'] = "Match $status successfully.";

        // Notify the other party if needed
        if ($status == 'confirmed') {
            // Notify both parties that match is confirmed (they can now resolve)
            $notifModel->create($lostOwner, 'match', "Match confirmed. You can now resolve it when the item is returned.", BASE_URL . "index.php?page=matches/view");
            $notifModel->create($foundOwner, 'match', "Match confirmed. You can now resolve it when the item is returned.", BASE_URL . "index.php?page=matches/view");
        } elseif ($status == 'resolved') {
            // Notify both that item is resolved
            $notifModel->create($lostOwner, 'match', "Match resolved. The lost item has been marked as returned.", BASE_URL . "index.php?page=matches/view");
            $notifModel->create($foundOwner, 'match', "Match resolved. The found item has been marked as claimed.", BASE_URL . "index.php?page=matches/view");
        }
    } else {
        $_SESSION['error'] = "Failed to update match. It may have been already updated.";
    }
    redirect('index.php?page=matches/view');
} else {
    // Default: show matches view
    require_once __DIR__ . '/../views/matches/my_matches.php';
}
?>