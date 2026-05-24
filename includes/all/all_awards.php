<?php
require_once(__DIR__ . '/../../class/database.php');
$database = new Database();
$db = $database->getConnection();

$awards = [];
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

try {
	if ($db instanceof PDO) {
		$query = "SELECT award_title, recipient_name, award_image, description, award_year, created_at FROM awards ORDER BY award_year DESC, created_at DESC";
		$awards = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
	}
} catch (PDOException $e) {
	$awards = [];
}

$awardYears = [];
foreach ($awards as $award) {
	$year = trim((string) ($award['award_year'] ?? ''));
	if ($year !== '') {
		$awardYears[$year] = true;
	}
}

$awardYears = array_keys($awardYears);
rsort($awardYears, SORT_NATURAL);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>All Awards - Faculty Union</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="../../images/facultyunion.png">
	<style>
		:root {
			--maroon: #8c1d1d;
			--maroon-dark: #671414;
			--gold: #d4af37;
			--bg: #f5f2ed;
			--card: #ffffff;
			--text: #1f2937;
			--muted: #6b7280;
			--line: #e5ddd7;
		}

		* { box-sizing: border-box; }

		body {
			background:
				radial-gradient(circle at top left, rgba(140, 29, 29, 0.08), transparent 32%),
				linear-gradient(180deg, #fbf8f4 0%, #f3ede6 100%);
			color: var(--text);
			overflow-x: hidden;
		}

		.sidebar {
			height: 100vh;
			background: rgba(255, 255, 255, 0.92);
			backdrop-filter: blur(18px);
			border-right: 1px solid var(--line);
			position: fixed;
			padding-top: 20px;
			z-index: 1050;
			transition: all 0.3s ease;
			width: 250px;
			left: 0;
			top: 0;
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
			transform: translateX(3px);
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
			width: 300px;
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

		.filter-chip {
			border: 1px solid var(--line);
			background: #fff;
			color: #4b5563;
			border-radius: 999px;
			padding: 10px 16px;
			font-weight: 700;
			transition: 0.2s ease;
		}

		.filter-chip:hover,
		.filter-chip.active {
			background: var(--maroon);
			color: #fff;
			border-color: var(--maroon);
		}

		.year-filter {
			max-width: 220px;
			border-radius: 999px;
			padding: 10px 16px;
			border: 1px solid var(--line);
			box-shadow: none;
		}

		.gallery-body {
			padding: 20px;
		}

		.award-card {
			background: var(--card);
			border: 1px solid rgba(229, 221, 215, 0.95);
			border-radius: 18px;
			overflow: hidden;
			height: 100%;
			box-shadow: 0 12px 26px rgba(31, 41, 55, 0.05);
			transition: transform 0.25s ease, box-shadow 0.25s ease;
			cursor: pointer;
		}

		.award-card:hover {
			transform: translateY(-6px);
			box-shadow: 0 20px 40px rgba(31, 41, 55, 0.1);
		}

		.media-wrapper {
			position: relative;
			width: 100%;
			padding-top: 75%;
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
			background: linear-gradient(180deg, rgba(0, 0, 0, 0.02), rgba(0, 0, 0, 0.5));
			display: flex;
			align-items: flex-end;
			justify-content: space-between;
			padding: 14px;
			color: #fff;
		}

		.award-badge {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			padding: 7px 11px;
			border-radius: 999px;
			font-size: 0.72rem;
			font-weight: 800;
			letter-spacing: 0.04em;
			text-transform: uppercase;
			background: rgba(212, 175, 55, 0.95);
			color: #1f2937;
		}

		.award-body {
			padding: 16px;
		}

		.award-title {
			font-size: 1rem;
			font-weight: 800;
			color: var(--text);
			margin-bottom: 8px;
			line-height: 1.3;
		}

		.award-recipient {
			color: var(--muted);
			font-size: 0.92rem;
			margin-bottom: 14px;
			min-height: 42px;
		}

		.award-meta {
			display: grid;
			gap: 8px;
		}

		.meta-row {
			display: flex;
			align-items: center;
			gap: 10px;
			font-size: 0.86rem;
			color: #4b5563;
			background: #faf7f4;
			border: 1px solid #f0e8e1;
			border-radius: 12px;
			padding: 9px 11px;
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
			background: rgba(255, 255, 255, 0.9);
			border: 1px dashed var(--line);
			border-radius: 22px;
			padding: 48px 20px;
			text-align: center;
			color: var(--muted);
		}

		.back-button {
			border-radius: 999px;
			padding: 10px 16px;
			border: 1px solid var(--line);
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
	</style>
</head>
<body>

<div class="sidebar" id="sidebar">
	<div class="text-center mb-4 px-3 d-flex justify-content-between align-items-start">
		<div class="w-100">
			<img src="<?php echo htmlspecialchars(resolveAssetPath('images/facultyunion.png', $assetBase), ENT_QUOTES, 'UTF-8'); ?>" alt="Logo" style="max-height: 80px;">
			<h5 class="fw-bold mb-0 mt-3" style="color: var(--maroon);">Faculty Union</h5>
			<small class="text-muted">Awards Center</small>
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
	<div class="gallery-header d-flex align-items-center justify-content-between px-3 px-md-4 py-3">
		<div class="d-flex align-items-center">
			<button class="mobile-toggle" type="button" onclick="toggleSidebar()">
				<i class="fas fa-bars"></i>
			</button>
			<h4 class="mb-0 fw-bold d-none d-sm-block">All Awards</h4>
		</div>
		<div class="search-container position-relative">
			<i class="fas fa-search"></i>
			<input type="text" id="awardSearch" class="form-control" placeholder="Search awards..." onkeyup="applyFilters()">
		</div>
	</div>

	<div class="gallery-body">
         <div class="d-flex justify-content-start mb-3">
            <button class="btn" id="backButton" onclick="window.location.href='../../index.php';" aria-label="Go back">
                <i class="fas fa-arrow-left me-2"></i>Back
        </button>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-end mb-3 mt-2">
			<label class="visually-hidden" for="awardYearFilter">Filter awards by year</label>
			<select id="awardYearFilter" class="form-select form-select-sm year-filter" onchange="applyFilters()">
				<option value="all">All years</option>
				<?php foreach ($awardYears as $year): ?>
					<option value="<?php echo htmlspecialchars($year, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($year, ENT_QUOTES, 'UTF-8'); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="row g-3 g-md-4" id="awards-grid">
			<?php if (!empty($awards)): ?>
				<?php foreach ($awards as $award): ?>
					<?php
						$awardYear = $award['award_year'] ?? '';
						$currentYear = date('Y');
						$awardStatus = ($awardYear !== '' && (int) $awardYear >= ((int) $currentYear - 2)) ? 'recent' : 'old';
						$title = $award['award_title'] ?? '';
						$recipient = $award['recipient_name'] ?? '';
						$image = resolveAssetPath($award['award_image'] ?? '', $assetBase);
						$cardData = htmlspecialchars(json_encode($award), ENT_QUOTES, 'UTF-8');
					?>
					<div class="col-12 col-md-6 col-lg-4 award-card-wrap"
						 data-type="award"
						 data-award-status="<?php echo htmlspecialchars($awardStatus, ENT_QUOTES, 'UTF-8'); ?>"
						 data-title="<?php echo htmlspecialchars(strtolower($title), ENT_QUOTES, 'UTF-8'); ?>"
						 data-recipient="<?php echo htmlspecialchars(strtolower($recipient), ENT_QUOTES, 'UTF-8'); ?>"
						 data-year="<?php echo htmlspecialchars((string) $awardYear, ENT_QUOTES, 'UTF-8'); ?>">
						<div class="award-card" onclick="viewAwardDetails(<?php echo $cardData; ?>)">
							<div class="media-wrapper">
								<img src="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
								<div class="media-overlay">
									<span class="award-badge">
										<i class="fas fa-award" style="font-size: 0.65rem;"></i>
										<?php echo htmlspecialchars($awardYear ?: 'TBA', ENT_QUOTES, 'UTF-8'); ?>
									</span>
								</div>
							</div>
							<div class="award-body">
								<div class="award-title text-truncate"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></div>
								<div class="award-recipient"><?php echo htmlspecialchars($recipient ?: 'Recipient not listed', ENT_QUOTES, 'UTF-8'); ?></div>
								<div class="award-meta">
									<div class="meta-row">
										<i class="fas fa-user"></i>
										<span><?php echo htmlspecialchars($recipient ?: 'Recipient not listed', ENT_QUOTES, 'UTF-8'); ?></span>
									</div>
									<div class="meta-row">
										<i class="fas fa-calendar"></i>
										<span><?php echo htmlspecialchars($awardYear ?: 'Year not set', ENT_QUOTES, 'UTF-8'); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<div class="col-12">
					<div class="empty-state">
						<i class="fas fa-award fa-2x mb-3" style="color: var(--maroon);"></i>
						<h5 class="fw-bold mb-2">No awards available</h5>
						<p class="mb-0">There are no records in the awards table yet.</p>
					</div>
				</div>
			<?php endif; ?>
			<div class="col-12 d-none" id="awardsEmptyState">
				<div class="empty-state">
					<i class="fas fa-award fa-2x mb-3" style="color: var(--maroon);"></i>
					<h5 class="fw-bold mb-2" id="awardsEmptyTitle">No Awards for this year</h5>
					<p class="mb-0" id="awardsEmptyMessage">There are no awards recorded for the selected year.</p>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="awardDetailsModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content border-0 shadow">
			<div class="modal-header border-0 pb-0">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-3 p-md-4" id="awardModalBody"></div>
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
	document.querySelectorAll('.filter-chip').forEach(link => link.classList.remove('active'));
	if (element) {
		element.classList.add('active');
	}
	currentCategory = category;
	if (window.innerWidth < 992) {
		toggleSidebar();
	}
	applyFilters();
}

function applyFilters() {
	const searchTerm = document.getElementById('awardSearch').value.toLowerCase();
	const selectedYear = document.getElementById('awardYearFilter').value;
	const cards = document.querySelectorAll('.award-card-wrap');
	const emptyState = document.getElementById('awardsEmptyState');
	const emptyTitle = document.getElementById('awardsEmptyTitle');
	const emptyMessage = document.getElementById('awardsEmptyMessage');
	let visibleCount = 0;

	cards.forEach(card => {
		const title = card.getAttribute('data-title') || '';
		const recipient = card.getAttribute('data-recipient') || '';
		const year = card.getAttribute('data-year') || '';
		const awardStatus = card.getAttribute('data-award-status');
		const matchesSearch = title.includes(searchTerm) || recipient.includes(searchTerm) || year.includes(searchTerm);
		const matchesYear = selectedYear === 'all' || year === selectedYear;

		let matchesCategory = currentCategory === 'all';

		if (currentCategory === 'recent' || currentCategory === 'old') {
			matchesCategory = awardStatus === currentCategory;
		}

		if (matchesSearch && matchesCategory && matchesYear) {
			card.style.display = '';
			visibleCount += 1;
		} else {
			card.style.display = 'none';
		}
	});

	if (emptyState) {
		if (visibleCount === 0) {
			emptyState.classList.remove('d-none');
			if (emptyTitle && emptyMessage) {
				if (selectedYear !== 'all') {
					emptyTitle.textContent = 'No Awards for this year';
					emptyMessage.textContent = 'There are no awards recorded for the selected year.';
				} else {
					emptyTitle.textContent = 'No awards found';
					emptyMessage.textContent = 'There are no awards matching your current search.';
				}
			}
		} else {
			emptyState.classList.add('d-none');
		}
	}
}

function viewAwardDetails(awardData) {
	const modalBody = document.getElementById('awardModalBody');
	const displayYear = awardData.award_year ? String(awardData.award_year) : 'TBA';
	const mediaHtml = `<img src="${escapeHtml(resolveAssetUrl(awardData.award_image || 'images/facultyunion.png'))}" class="img-fluid rounded-4 shadow-sm" alt="${escapeHtml(awardData.award_title || '')}">`;
	const infoHtml = `
		<div class="detail-info"><i class="fas fa-user"></i> <strong>Recipient:</strong> ${escapeHtml(awardData.recipient_name || 'Not listed')}</div>
		<div class="detail-info"><i class="fas fa-calendar"></i> <strong>Year:</strong> ${escapeHtml(displayYear)}</div>
		<div class="detail-info"><i class="fas fa-award"></i> <strong>Title:</strong> ${escapeHtml(awardData.award_title || 'Untitled award')}</div>
	`;

	modalBody.innerHTML = `
		<div class="row g-3">
			<div class="col-md-5">
				${mediaHtml}
			</div>
			<div class="col-md-7">
				<h4 class="fw-bold mb-1" style="color: var(--maroon);">${escapeHtml(awardData.award_title || '')}</h4>
				<p class="text-muted small mb-3">${escapeHtml(awardData.recipient_name || 'Recipient not listed')}</p>
				${infoHtml}
				<hr>
				<p class="small text-secondary mb-0">${escapeHtml(awardData.description || 'No description available.')}</p>
			</div>
		</div>`;

	new bootstrap.Modal(document.getElementById('awardDetailsModal')).show();
}

function resolveAssetUrl(path) {
	path = (path || '').trim();
	if (!path) {
		return '../../images/facultyunion.png';
	}

	if (/^(?:https?:)?\/\//i.test(path) || path.startsWith('/')) {
		return path;
	}

	return '../../' + path.replace(/^\.?\/?/, '');
}

function escapeHtml(value) {
	return String(value)
		.replace(/&/g, '&amp;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;')
		.replace(/"/g, '&quot;')
		.replace(/'/g, '&#039;');
}
</script>
</body>
</html>
