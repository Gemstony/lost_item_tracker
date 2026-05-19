<?php
// index.php - Entry point
require_once __DIR__ . '/includes/config.php';

// Get the page parameter (default to 'welcome')
$page = $_GET['page'] ?? 'welcome';

// Routing based on page parameter
switch ($page) {
    // Public pages (no login required)
    case 'welcome':
        require_once __DIR__ . '/welcome.php';
        break;
    
    case 'login':
    case 'register':
    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        break;
    
    // Protected pages (require login)
    case 'dashboard':
        if (!isLoggedIn()) redirect('login');
        require_once __DIR__ . '/views/dashboard/index.php';
        break;
    
    case 'lost_items/report':
        if (!isLoggedIn()) redirect('login');
        require_once __DIR__ . '/controllers/LostItemController.php';
        break;
    
    case 'lost_items/list':
        if (!isLoggedIn()) redirect('login');
        require_once __DIR__ . '/views/lost_items/list.php';
        break;
    
    case 'found_items/report':
        if (!isLoggedIn()) redirect('login');
        require_once __DIR__ . '/controllers/FoundItemController.php';
        break;
    
    case 'found_items/list':
        if (!isLoggedIn()) redirect('login');
        require_once __DIR__ . '/views/found_items/list.php';
        break;
    
    case 'found_items/map':
        if (!isLoggedIn()) redirect('login');
        require_once __DIR__ . '/views/found_items/map_view.php';
        break;
    
    case 'incidents/report':
        if (!isLoggedIn()) redirect('login');
        require_once __DIR__ . '/controllers/IncidentController.php';
        break;
    
    case 'incidents/list':
        if (!isLoggedIn()) redirect('login');
        require_once __DIR__ . '/views/incidents/list.php';
        break;
    
    case 'matches/view':
        if (!isLoggedIn()) redirect('login');
        require_once __DIR__ . '/views/matches/my_matches.php';
        break;
    
    case 'admin/dashboard':
        if (!isAdmin()) redirect('dashboard');
        require_once __DIR__ . '/views/admin/dashboard.php';
        break;
    
    case 'admin/users':
        if (!isAdmin()) redirect('dashboard');
        require_once __DIR__ . '/views/admin/users.php';
        break;
    
    case 'admin/reports':
        if (!isAdmin()) redirect('dashboard');
        require_once __DIR__ . '/views/admin/reports.php';
        break;
    
    default:
        http_response_code(404);
        echo "<h1>404 - Page not found</h1>";
        break;
}
?>