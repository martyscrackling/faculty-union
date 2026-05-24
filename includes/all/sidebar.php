<?php $currentPage = $currentPage ?? basename($_SERVER['PHP_SELF']); ?>
<div class="sidebar" id="sidebar">
	<div class="text-center mb-4 px-3 d-flex justify-content-between align-items-start">
		<div class="w-100">
			<img src="<?php echo htmlspecialchars(resolveAssetPath('images/facultyunion.png', $assetBase), ENT_QUOTES, 'UTF-8'); ?>" alt="Logo" style="max-height: 80px;">
			<h5 class="fw-bold mb-0 mt-3" style="color: var(--maroon);">Faculty Union</h5>
			<small class="text-muted">Events Center</small>
		</div>
		<button class="btn d-lg-none" type="button" onclick="toggleSidebar()" aria-label="Close menu">
			<i class="fas fa-times"></i>
		</button>
	</div>

	<nav class="nav flex-column">
		<a href="all_events.php" class="nav-link<?php echo $currentPage === 'all_events.php' ? ' active' : ''; ?>" onclick="setCategory('all', this)">
			<i class="fas fa-th-large"></i> Events
		</a>
		<a href="all_awards.php" class="nav-link<?php echo $currentPage === 'all_awards.php' ? ' active' : ''; ?>" onclick="setCategory('upcoming', this)">
			<i class="fas fa-calendar-check"></i> Awards
		</a>
		<a href="all_videos.php" class="nav-link<?php echo $currentPage === 'all_videos.php' ? ' active' : ''; ?>" onclick="setCategory('past', this)">
			<i class="fas fa-history"></i> Videos
		</a>
		<a href="all_gallery.php" class="nav-link<?php echo $currentPage === 'all_gallery.php' ? ' active' : ''; ?>" onclick="setCategory('past', this)">
			<i class="fas fa-image"></i> Gallery
		</a>
	</nav>
</div>