    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    
   <style>
        :root { --maroon: #8c1d1d; --gold: #d4af37; }
        body { background: #f4f7f6; }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: var(--maroon);
            color: #fff;
            padding: 24px 0;
            overflow-y: auto;
            box-shadow: 0 0 24px rgba(0, 0, 0, 0.12);
        }
        .sidebar .brand {
            padding: 0 24px 18px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            margin-bottom: 8px;
        }
        .sidebar .brand img {
            max-height: 100px;
            margin-bottom: 12px;
        }
        .sidebar .brand h5 {
            margin: 0;
            font-weight: 700;
        }
        .sidebar .brand small {
            color: rgba(255, 255, 255, 0.75);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.84);
            padding: 14px 24px;
            display: block;
            text-decoration: none;
            border-left: 4px solid transparent;
            transition: 0.2s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(0, 0, 0, 0.18);
            color: var(--gold);
            border-left-color: var(--gold);
        }
        .sidebar .nav-link i { width: 20px; margin-right: 10px; }
        .main-content { margin-left: 260px; padding: 40px; }
        .table thead { background: #8c1d1d; color: white; }
        .btn-maroon { background: #8c1d1d; color: white; border: none; }
        .btn-maroon:hover { background: #d4af37; color: black; }
        @media (max-width: 991.98px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>

    <div class="sidebar">
    <div class="brand">
        <img src="../images/facultyunion.png" alt="WMSU Faculty Union logo">
        <h5>WMSU-FU</h5>
        <small>Admin Panel</small>
    </div>
    <a href="dashboard.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? ' active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a>
    <a href="manage_site.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'manage_site.php' ? ' active' : ''; ?>"><i class="fas fa-image"></i> Manage Logo &amp; Title</a>
    <a href="manage_officers.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'manage_officers.php' ? ' active' : ''; ?>"><i class="fas fa-users"></i> Manage Officers</a>
    <a href="manage_vision.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'manage_vision.php' ? ' active' : ''; ?>"><i class="fas fa-list"></i> Vision &amp; Objectives</a>
    <a href="manage_contact.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'manage_contact.php' ? ' active' : ''; ?>"><i class="fas fa-address-book"></i> Manage Contact</a>
    <a href="manage_events.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'manage_events.php' ? ' active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Manage Events</a>
    <a href="manage_awards.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'manage_awards.php' ? ' active' : ''; ?>"><i class="fas fa-award"></i> Manage Awards</a>
    <a href="manage_videos.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'manage_videos.php' ? ' active' : ''; ?>"><i class="fas fa-play-circle"></i> Manage Videos</a>
   
</div>