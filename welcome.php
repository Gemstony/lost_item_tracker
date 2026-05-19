<?php
$pageTitle = 'Digital Tracking & Reporting System';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero {
            color: white;
            text-align: center;
            padding: 60px 20px 40px;
        }
        .hero h1 {
            font-size: 2.8rem;
            font-weight: bold;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin: 40px auto;
            max-width: 1200px;
        }
        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: transform 0.3s;
            flex: 1;
            min-width: 250px;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-card i {
            font-size: 3rem;
            color: #0d6efd;
            margin-bottom: 20px;
        }
        .btn-login {
            background: white;
            color: #0d6efd;
            padding: 12px 40px;
            font-size: 1.2rem;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        footer {
            text-align: center;
            color: rgba(255,255,255,0.7);
            padding: 30px;
        }
    </style>
</head>
<body>
<div class="hero">
    <h1><i class="fas fa-search"></i> Digital Tracking & Reporting System</h1>
    <p>For Lost Items and Incidents in Tanzanian Universities</p>
    <a href="index.php?page=login" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login to Dashboard</a>
</div>
<div class="card-container">
    <div class="feature-card"><i class="fas fa-frown"></i><h3>Report Lost Items</h3><p>Quickly report any lost property on campus.</p></div>
    <div class="feature-card"><i class="fas fa-smile"></i><h3>Report Found Items</h3><p>Help others recover their belongings – with GPS location.</p></div>
    <div class="feature-card"><i class="fas fa-map-marker-alt"></i><h3>GPS Navigation</h3><p>Navigate directly to where an item was found.</p></div>
    <div class="feature-card"><i class="fas fa-exclamation-triangle"></i><h3>Incident Management</h3><p>Report theft, safety issues, and track resolution.</p></div>
</div>
<footer><p>&copy; <?= date('Y') ?> National Institute of Transport - All rights reserved.</p></footer>
</body>
</html>