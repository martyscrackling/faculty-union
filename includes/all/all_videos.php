<?php
require_once(__DIR__ . '/../../class/database.php');
$database = new Database();
$db = $database->getConnection();

$videos = [];
$assetBase = '../../';
$currentPage = basename($_SERVER['PHP_SELF']);

function resolveAssetPath($path, $basePath)
{
	$path = trim((string) $path);
	if ($path === '') {
		return $basePath . 'images/facultyunion.png';
	}

	if (preg_match('~^(?:https?:)?//~i', $path) || strpos($path, '/') === 0) {
		return $path;
	}

	return $basePath . ltrim($path, './');
}

function youtubeIdFromSource($source)
{
	$source = trim((string) $source);
	if ($source === '') {
		return '';
	}

	$patterns = [
		'~(?:youtube\.com/embed/|youtube\.com/watch\?v=|youtu\.be/)([A-Za-z0-9_-]{11})~i',
		'~youtube\.com/shorts/([A-Za-z0-9_-]{11})~i'
	];

	foreach ($patterns as $pattern) {
		if (preg_match($pattern, $source, $matches)) {
			return $matches[1];
		}
	}

	return '';
}

try {
	if ($db instanceof PDO) {
		$query = "SELECT video_title, video_source, video_type, thumbnail, created_at FROM admin_videos ORDER BY created_at DESC";
		$videos = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
	}
} catch (PDOException $e) {
	$videos = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>All Videos - Faculty Union</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="../../images/facultyunion.png">

	<style>
		:root {
			--maroon: #8c1d1d;
			--maroon-dark: #671414;
			--gold: #d4af37;
		}

		* { box-sizing: border-box; }

		body {
			background: #f8f9fa;
			overflow-x: hidden;
		}

		.sidebar {
			height: 100vh;
			background: #fff;
			border-right: 1px solid #dee2e6;
			position: fixed;
			padding-top: 20px;
			z-index: 1050;
			transition: all 0.3s;
			width: 250px;
			left: 0;
			top: 0;
		}

		.sidebar-backdrop {
			position: fixed;
			inset: 0;
			background: rgba(0, 0, 0, 0.35);
			z-index: 1040;
			display: none;
		}

		.sidebar-backdrop.active {
			display: block;
		}

		.nav-link {
			color: #4b5563;
			font-weight: 600;
			padding: 12px 20px;
			transition: 0.25s ease;
			cursor: pointer;
			border-radius: 0 999px 999px 0;
			margin-right: 10px;
		}

		.nav-link:hover,
		.nav-link.active {
			background: linear-gradient(135deg, var(--maroon), var(--maroon-dark));
			color: #fff !important;
		}

		.nav-link i { margin-right: 10px; width: 20px; text-align: center; }

		.content-wrap {
			margin-left: 250px;
			width: calc(100% - 250px);
			min-height: 100vh;
		}

		.gallery-header {
			position: sticky;
			top: 0;
			z-index: 999;
			background: white;
			border-bottom: 1px solid #dee2e6;
		}

		.search-container {
			position: relative;
			max-width: 100%;
			width: 320px;
		}

		.search-container .form-control {
			border-radius: 20px;
			padding-left: 40px;
		}

		.search-container i {
			position: absolute;
			left: 15px;
			top: 50%;
			transform: translateY(-50%);
			color: #adb5bd;
		}

		.gallery-body {
			padding: 20px;
		}

		.video-card {
			background: #fff;
			border-radius: 12px;
			overflow: hidden;
			height: 100%;
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
			border: 1px solid #eee;
			transition: 0.3s;
			cursor: pointer;
		}

		.video-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
		}

		.media-wrapper {
			position: relative;
			width: 100%;
			padding-top: 56.25%;
			background: linear-gradient(135deg, #111827, #374151);
		}

		.media-wrapper img {
			position: absolute;
			inset: 0;
			width: 100%;
			height: 100%;
			object-fit: cover;
		}

		.media-overlay {
			position: absolute;
			inset: 0;
			background: linear-gradient(180deg, rgba(0, 0, 0, 0.05), rgba(0, 0, 0, 0.55));
			display: flex;
			align-items: center;
			justify-content: center;
			color: #fff;
		}

		.play-icon {
			font-size: 2.6rem;
			text-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
		}

		.video-badge {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			padding: 7px 10px;
			border-radius: 999px;
			font-size: 0.72rem;
			font-weight: 800;
			letter-spacing: 0.04em;
			text-transform: uppercase;
			background: #dc3545;
			color: #fff;
		}

		.video-body {
			padding: 16px;
		}

		.video-title {
			font-size: 0.98rem;
			font-weight: 700;
			color: #212529;
			margin-bottom: 12px;
			line-height: 1.35;
			min-height: 44px;
		}

		.video-meta {
			display: grid;
			gap: 8px;
		}

		.meta-row {
			display: flex;
			align-items: center;
			gap: 10px;
			font-size: 0.84rem;
			color: #495057;
			background: #f8f9fa;
			border-radius: 10px;
			padding: 8px 10px;
		}

		.meta-row i { color: var(--maroon); width: 16px; text-align: center; }

		.mobile-toggle {
			display: none;
			background: var(--maroon);
			color: #fff;
			border: none;
			padding: 9px 15px;
			border-radius: 10px;
			margin-right: 15px;
		}

		.modal-content {
			border-radius: 24px;
			overflow: hidden;
		}

		.detail-info {
			font-size: 0.9rem;
			margin-bottom: 10px;
			padding: 10px 12px;
			background: #f8f8f8;
			border-radius: 10px;
		}

		.detail-info i {
			color: var(--maroon);
			width: 18px;
			margin-right: 8px;
		}

		.empty-state {
			background: #fff;
			border: 1px dashed #ced4da;
			border-radius: 16px;
			padding: 48px 20px;
			text-align: center;
			color: #6c757d;
		}

		.back-button {
			border-radius: 999px;
			padding: 10px 16px;
			border: 1px solid #dee2e6;
			background: #fff;
			font-weight: 700;
		}

		@media (max-width: 991.98px) {
			.sidebar {
				width: 0;
				transform: translateX(-100%);
				visibility: hidden;
			}

			.sidebar.active {
				width: 250px;
				transform: translateX(0);
				visibility: visible;
			}

			.content-wrap {
				margin-left: 0;
				width: 100%;
			}

			.mobile-toggle {
				display: inline-flex !important;
				align-items: center;
				justify-content: center;
			}
		}

		@media (max-width: 767.98px) {
			.search-container { width: 100%; }

			.video-title { min-height: auto; }

			.gallery-body { padding: 14px; }
		}
	</style>
</head>
<body>

<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

<div class="sidebar" id="sidebar">
	<div class="text-center mb-4 px-3 d-flex justify-content-between align-items-start">
		<div class="w-100">
			<img src="<?php echo htmlspecialchars(resolveAssetPath('images/facultyunion.png', $assetBase), ENT_QUOTES, 'UTF-8'); ?>" alt="Logo" style="max-height: 80px;">
			<h5 class="fw-bold mb-0 mt-3" style="color: var(--maroon);">Faculty Union</h5>
			<small class="text-muted">Video Center</small>
		</div>
		<button class="btn d-lg-none" type="button" onclick="toggleSidebar()" aria-label="Close menu">
			<i class="fas fa-times"></i>
		</button>
	</div>

	<nav class="nav flex-column" aria-label="Awards navigation">
		<a href="all_events.php" class="nav-link<?php echo $currentPage === 'all_events.php' ? ' active' : ''; ?>" role="link">
			<i class="fas fa-calendar-days" aria-hidden="true" title="Events"></i> Events
		</a>
		<a href="all_awards.php" class="nav-link<?php echo $currentPage === 'all_awards.php' ? ' active' : ''; ?>" role="link">
			<i class="fas fa-trophy" aria-hidden="true" title="Awards"></i> Awards
		</a>
		<a href="all_videos.php" class="nav-link<?php echo $currentPage === 'all_videos.php' ? ' active' : ''; ?>" role="link">
			<i class="fas fa-video" aria-hidden="true" title="Videos"></i> Videos
		</a>
		<a href="../../gallery.php" class="nav-link<?php echo $currentPage === 'gallery.php' ? ' active' : ''; ?>" role="link">
			<i class="fas fa-images" aria-hidden="true" title="Gallery"></i> Gallery
		</a>
	</nav>
</div>

<div class="content-wrap">
	<div class="gallery-header px-3 px-md-4 py-3">
		<div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
			<div class="d-flex align-items-center">
				<button class="mobile-toggle" type="button" onclick="toggleSidebar()">
					<i class="fas fa-bars"></i>
				</button>
				<h4 class="mb-0 fw-bold">All Videos</h4>
			</div>
			<div class="search-container">
				<i class="fas fa-search"></i>
				<input type="text" id="videoSearch" class="form-control" placeholder="Search videos..." onkeyup="applyFilters()">
			</div>
		</div>

	</div>

	<div class="gallery-body">
        <div class="d-flex justify-content-start mb-3">
            <button class="btn" id="backButton" onclick="window.location.href='../../index.php';" aria-label="Go back">
                <i class="fas fa-arrow-left me-2"></i>Back
        </button>
        </div>
		<div class="row g-3 g-md-4" id="videoGrid">
			<?php if (!empty($videos)): ?>
				<?php foreach ($videos as $video): ?>
					<?php
						$title = trim((string) ($video['video_title'] ?? 'Untitled Video'));
						$type = strtolower(trim((string) ($video['video_type'] ?? 'uploaded')));
						$source = trim((string) ($video['video_source'] ?? ''));
						$youtubeId = youtubeIdFromSource($source);

						$thumbnail = trim((string) ($video['thumbnail'] ?? ''));
						if ($thumbnail !== '') {
							$thumbnail = resolveAssetPath($thumbnail, $assetBase);
						} elseif ($type === 'youtube' && $youtubeId !== '') {
							$thumbnail = 'https://img.youtube.com/vi/' . $youtubeId . '/hqdefault.jpg';
						} else {
							$thumbnail = resolveAssetPath('images/facultyunion.png', $assetBase);
						}

						$createdAtRaw = trim((string) ($video['created_at'] ?? ''));
						$createdAt = $createdAtRaw !== '' ? date('M d, Y', strtotime($createdAtRaw)) : 'Not specified';
					?>
					<div class="col-12 col-sm-6 col-lg-4 video-item"
						 data-title="<?php echo htmlspecialchars(strtolower($title), ENT_QUOTES, 'UTF-8'); ?>">
						<div class="video-card" onclick='openVideoModal(<?php echo htmlspecialchars(json_encode([
							"title" => $title,
							"source" => $source,
							"type" => $type,
							"created_at" => $createdAt,
							"thumbnail" => $thumbnail
						]), ENT_QUOTES, "UTF-8"); ?>)'>
							<div class="media-wrapper">
								<img src="<?php echo htmlspecialchars($thumbnail, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
								<div class="media-overlay">
									<i class="fas fa-play-circle play-icon"></i>
								</div>
							</div>
							<div class="video-body">
								<div class="d-flex justify-content-between align-items-center mb-2 gap-2">
									<span class="video-badge">
										<i class="fas fa-film"></i>
										<?php echo $type === 'youtube' ? 'YouTube' : 'Uploaded'; ?>
									</span>
								</div>
								<h5 class="video-title"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h5>
								<div class="video-meta">
									<div class="meta-row">
										<i class="fas fa-calendar-alt"></i>
										<span><?php echo htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8'); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<div class="col-12">
					<div class="empty-state">
						<i class="fas fa-video fa-2x mb-3"></i>
						<h5 class="fw-bold mb-2">No videos available</h5>
						<p class="mb-0">Upload videos from the admin panel to populate this page.</p>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content border-0 shadow">
			<div class="modal-header border-0 pb-0">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-3 p-md-4" id="videoModalBody"></div>
		</div>
	</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
	const sidebar = document.getElementById('sidebar');
	const backdrop = document.getElementById('sidebarBackdrop');
	const isOpen = sidebar.classList.toggle('active');
	backdrop.classList.toggle('active', isOpen);
}

function closeSidebar() {
	document.getElementById('sidebar').classList.remove('active');
	document.getElementById('sidebarBackdrop').classList.remove('active');
}

function applyFilters() {
	const term = document.getElementById('videoSearch').value.toLowerCase();
	const cards = document.querySelectorAll('.video-item');

	cards.forEach(card => {
		const title = card.getAttribute('data-title') || '';
		const matchesSearch = title.includes(term);

		card.style.display = matchesSearch ? 'block' : 'none';
	});
}

function openVideoModal(video) {
	const body = document.getElementById('videoModalBody');
	const sourceType = (video.type || '').toLowerCase();
	let playerHtml = '';

	if (sourceType === 'youtube') {
		const separator = video.source.includes('?') ? '&' : '?';
		playerHtml = `<div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm mb-3 mb-md-0"><iframe src="${video.source}${separator}autoplay=1" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>`;
	} else {
		playerHtml = `<div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm mb-3 mb-md-0"><video controls autoplay><source src="${video.source}" type="video/mp4">Your browser does not support the video tag.</video></div>`;
	}

	body.innerHTML = `
		<div class="row g-3">
			<div class="col-md-8">${playerHtml}</div>
			<div class="col-md-4">
				<h4 class="fw-bold mb-2" style="color: var(--maroon);">${video.title || 'Untitled Video'}</h4>
				<div class="detail-info"><i class="fas fa-film"></i><strong>Source:</strong> ${sourceType === 'youtube' ? ' YouTube' : ' Uploaded'}</div>
				<div class="detail-info"><i class="fas fa-calendar-alt"></i><strong>Added:</strong> ${video.created_at || 'Not specified'}</div>
			</div>
		</div>`;

	new bootstrap.Modal(document.getElementById('videoModal')).show();
}

window.addEventListener('resize', function() {
	if (window.innerWidth >= 992) {
		closeSidebar();
	}
});
</script>
</body>
</html>
