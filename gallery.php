<?php
require_once('./class/database.php');
$database = new Database();
$db = $database->getConnection();

// 1. Fetch Awards
$awards = $db->query("SELECT award_title as title, recipient_name, award_image as path, description, award_year, 'award' as type, created_at FROM awards")->fetchAll(PDO::FETCH_ASSOC);

// 2. Fetch Videos
$videos = $db->query("SELECT video_title as title, video_source as path, 'video' as type, created_at, video_type FROM admin_videos")->fetchAll(PDO::FETCH_ASSOC);

// 3. Fetch Events - Including the event_start_date for filtering
$events = $db->query("SELECT title as title, subtitle, event_start_date, banner_path as path, location, event_time, admission, description, 'event' as type, created_at FROM events")->fetchAll(PDO::FETCH_ASSOC);

$gallery_items = array_merge($awards, $videos, $events);
usort($gallery_items, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Faculty Union</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --maroon: #8c1d1d; }
        body { background-color: #f8f9fa; overflow-x: hidden; }
        
        /* Sidebar Responsive Design */
        .sidebar { 
            height: 100vh; 
            background: white; 
            border-right: 1px solid #dee2e6; 
            position: fixed; 
            padding-top: 20px; 
            z-index: 1050; 
            transition: all 0.3s;
            width: 250px;
        }

        .nav-link { color: #495057; font-weight: 500; padding: 12px 20px; transition: 0.3s; cursor: pointer; border-radius: 0 25px 25px 0; margin-right: 10px; }
        .nav-link:hover, .nav-link.active { background: var(--maroon); color: white !important; }
        .nav-link i { margin-right: 10px; width: 20px; text-align: center; }

        /* Main Content Adjustment */
        .gallery-content { margin-left: 250px; transition: all 0.3s; width: calc(100% - 250px); }

        .gallery-header { 
            background: white; 
            padding: 15px 20px; 
            border-bottom: 1px solid #dee2e6; 
            position: sticky; 
            top: 0; 
            z-index: 999; 
        }

        .search-container { max-width: 100%; width: 300px; }
        .search-container .form-control { border-radius: 20px; padding-left: 40px; }
        .search-container i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #adb5bd; }

        .gallery-body { padding: 20px; }
        
        /* Gallery Cards */
        .gallery-item { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: 0.3s; height: 100%; border: 1px solid #eee; cursor: pointer; }
        .gallery-item:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        
        .media-wrapper { position: relative; width: 100%; padding-top: 56.25%; background: #000; }
        .media-wrapper img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }

        .item-details { padding: 15px; }
        .item-title { font-size: 0.95rem; font-weight: 600; margin-bottom: 8px; color: #212529; }
        
        /* Badges */
        .bg-award { background-color: #ffc107; color: #000; }
        .bg-video { background-color: #dc3545; color: #fff; }
        .bg-upcoming { background-color: #198754; color: #fff; }
        .bg-past { background-color: #6c757d; color: #fff; }

        .detail-info { font-size: 0.85rem; margin-bottom: 8px; padding: 8px; background: #f8f9fa; border-radius: 5px; }
        .detail-info i { color: var(--maroon); width: 18px; margin-right: 8px; }

        /* Responsive Mobile Tweak */
        @media (max-width: 991.98px) {
            .sidebar { width: 0; transform: translateX(-100%); visibility: hidden; }
            .sidebar.active { width: 250px; transform: translateX(0); visibility: visible; }
            .gallery-content { margin-left: 0; width: 100%; }
            .mobile-toggle { display: block !important; }
        }

        .mobile-toggle { display: none; background: var(--maroon); color: white; border: none; padding: 8px 15px; border-radius: 5px; margin-right: 15px; }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="text-center mb-4 px-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0" style="color: var(--maroon);">Faculty Union</h5>
            <small class="text-muted">Media Center</small>
        </div>
        <button class="btn d-lg-none" onclick="toggleSidebar()"><i class="fas fa-times"></i></button>
    </div>
    
    <nav class="nav flex-column">
        <a class="nav-link active" onclick="setCategory('all', this)">
            <i class="fas fa-th-large"></i> All Media
        </a>
        <a class="nav-link" onclick="setCategory('image_only', this)">
            <i class="fas fa-images"></i>Images
        </a>
        <a class="nav-link" onclick="setCategory('video', this)">
            <i class="fas fa-video"></i> Videos
        </a>
        <a class="nav-link" onclick="setCategory('award', this)">
            <i class="fas fa-award"></i> Awards
        </a>
        <hr class="mx-3">
        <small class="px-4 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Event Filtering</small>
        <a class="nav-link" onclick="setCategory('upcoming', this)">
            <i class="fas fa-calendar-check text-success"></i> Upcoming
        </a>
        <a class="nav-link" onclick="setCategory('past', this)">
            <i class="fas fa-history text-secondary"></i> Past Events
        </a>
        <hr class="mx-3">
        <a class="nav-link text-muted" href="index.php">
            <i class="fas fa-arrow-left"></i> Home
        </a>
    </nav>
</div>

<div class="gallery-content">
    <div class="gallery-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <button class="mobile-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h4 class="mb-0 fw-bold d-none d-sm-block">Gallery</h4>
        </div>
        <div class="search-container position-relative">
            <i class="fas fa-search"></i>
            <input type="text" id="gallerySearch" class="form-control" placeholder="Search..." onkeyup="applyFilters()">
        </div>
    </div>

    <div class="gallery-body">
        <div class="row g-3 g-md-4" id="gallery-grid">
            <?php 
            $today = date('Y-m-d');
            foreach ($gallery_items as $item): 
                $eventStatus = '';
                if ($item['type'] === 'event') {
                    $eventStatus = (isset($item['event_start_date']) && $item['event_start_date'] >= $today) ? 'upcoming' : 'past';
                }
            ?>
            <div class="col-6 col-md-6 col-lg-4 gallery-card" 
                 data-type="<?php echo $item['type']; ?>" 
                 data-event-status="<?php echo $eventStatus; ?>"
                 data-title="<?php echo strtolower(htmlspecialchars($item['title'])); ?>">
                
                <div class="gallery-item" onclick="viewDetails(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                    <div class="media-wrapper">
                        <?php if ($item['type'] == 'video'): ?>
                            <i class="fas fa-play-circle position-absolute top-50 start-50 translate-middle text-white fa-2x fa-md-3x" style="z-index: 1;"></i>
                            <img src="https://img.youtube.com/vi/<?php 
                                preg_match('/embed\/([^\/\?]+)/', $item['path'], $id); 
                                echo $id[1] ?? 'default'; 
                            ?>/0.jpg">
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars($item['path']); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="item-details">
                        <div class="item-title text-truncate"><?php echo htmlspecialchars($item['title']); ?></div>
                        <?php 
                            $badgeClass = $item['type'];
                            $label = strtoupper($item['type']);
                            if ($eventStatus === 'upcoming') { $badgeClass = 'upcoming'; $label = 'UPCOMING'; }
                            if ($eventStatus === 'past') { $badgeClass = 'past'; $label = 'PAST'; }
                        ?>
                        <span class="badge bg-<?php echo $badgeClass; ?> rounded-pill" style="font-size: 0.7rem;"><?php echo $label; ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 p-md-4" id="modalBodyContent"></div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
let currentCategory = 'all';

function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
}

function setCategory(category, element) {
    document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
    element.classList.add('active');
    currentCategory = category;
    if(window.innerWidth < 992) toggleSidebar(); 
    applyFilters();
}

function applyFilters() {
    const searchTerm = document.getElementById('gallerySearch').value.toLowerCase();
    const cards = document.querySelectorAll('.gallery-card');
    
    cards.forEach(card => {
        const title = card.getAttribute('data-title');
        const type = card.getAttribute('data-type');
        const eventStatus = card.getAttribute('data-event-status');
        const matchesSearch = title.includes(searchTerm);
        
        let matchesCategory = (currentCategory === 'all') || 
                             (currentCategory === 'image_only' && (type === 'award' || type === 'event')) ||
                             ((currentCategory === 'upcoming' || currentCategory === 'past') && type === 'event' && eventStatus === currentCategory) ||
                             (type === currentCategory);

        card.style.display = (matchesSearch && matchesCategory) ? 'block' : 'none';
    });
}

function viewDetails(data) {
    const modalBody = document.getElementById('modalBodyContent');
    const isMobile = window.innerWidth < 768;

    if (currentCategory === 'image_only' || currentCategory === 'all') {
        if (data.type === 'video') renderDetailedView(data);
        else renderSimpleView(data);
    } else {
        renderDetailedView(data);
    }
    new bootstrap.Modal(document.getElementById('detailsModal')).show();
}

function renderSimpleView(data) {
    document.getElementById('modalBodyContent').innerHTML = `
        <div class="text-center">
            <img src="${data.path}" class="rounded img-fluid mb-3">
            <h5 class="fw-bold">${data.title}</h5>
        </div>`;
}

function renderDetailedView(data) {
    let mediaHtml = data.type === 'video' 
        ? `<div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm mb-3 mb-md-0">
            ${data.video_type === 'youtube' ? `<iframe src="${data.path}" allowfullscreen></iframe>` : `<video controls><source src="${data.path}" type="video/mp4"></video>`}
           </div>`
        : `<img src="${data.path}" class="img-fluid rounded mb-3 mb-md-0">`;

    let infoHtml = "";
    if (data.type === 'award') {
        infoHtml = `<div class="detail-info"><i class="fas fa-user"></i> <strong>Recipient:</strong> ${data.recipient_name}</div>
                    <div class="detail-info"><i class="fas fa-calendar"></i> <strong>Year:</strong> ${data.award_year}</div>`;
    } else if (data.type === 'event') {
        // Format the start date for display
        const dateOptions = { month: 'short', day: '2-digit', year: 'numeric' };
        const displayDate = new Date(data.event_start_date).toLocaleDateString('en-US', dateOptions);

        infoHtml = `<div class="detail-info"><i class="fas fa-calendar-alt"></i> <strong>Date:</strong> ${displayDate}</div>
                    <div class="detail-info"><i class="fas fa-clock"></i> <strong>Time:</strong> ${data.event_time}</div>
                    <div class="detail-info"><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> ${data.location}</div>`;
    }

    document.getElementById('modalBodyContent').innerHTML = `
        <div class="row g-3">
            <div class="col-md-5">${mediaHtml}</div>
            <div class="col-md-7">
                <h4 class="fw-bold mb-1" style="color: var(--maroon);">${data.title}</h4>
                <p class="text-muted small mb-3">${data.subtitle || ""}</p>
                ${infoHtml}
                <hr>
                <p class="small text-secondary">${data.description || "No description available."}</p>
            </div>
        </div>`;
}
</script>
</body>
</html>