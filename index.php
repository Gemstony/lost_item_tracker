<?php
session_start();
require_once 'config/db.php';
require_once 'includes/helpers.php';

// Get the 'page' parameter from URL (e.g., index.php?page=lost_items/list)
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard/index';

// Security: prevent directory traversal
$page = str_replace(['..', '/', '\\'], '', $page);

// Define which controller to load
$controllerMap = [
    'login'             => 'controllers/auth.php',
    'register'          => 'controllers/auth.php',
    'logout'            => 'controllers/auth.php',
    'dashboard/index'   => 'views/dashboard/index.php',
    'lost_items/report' => 'controllers/lost_items.php',
    'lost_items/list'   => 'views/lost_items/list.php',
    'found_items/report'=> 'controllers/found_items.php',
    'found_items/list'  => 'views/found_items/list.php',
    'found_items/map'   => 'views/found_items/map_view.php',
    'incidents/report'  => 'controllers/incidents.php',
    'incidents/list'    => 'views/incidents/list.php',
    'matches/view'      => 'views/matches/my_matches.php',
    'admin/dashboard'   => 'controllers/admin.php',
    'admin/reports'     => 'views/admin/reports.php',
];

if (array_key_exists($page, $controllerMap)) {
    require_once $controllerMap[$page];
} else {
    require_once 'views/errors/404.php';
}
?>