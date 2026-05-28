<?php
require_once('../class/database.php');
require_once('sidebar.php');
$database = new Database();
$db = $database->getConnection();
$navtext = "Videos";
require_once('navbar.php');

// --- HANDLE DELETE ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $stmt = $db->prepare("SELECT video_type, video_source FROM admin_videos WHERE id = ?");
    $stmt->execute([$id]);
    $video = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($video && $video['video_type'] == 'raw') {
        if (file_exists("../" . $video['video_source'])) {
            unlink("../" . $video['video_source']);
        }
    }

    $del_stmt = $db->prepare("DELETE FROM admin_videos WHERE id = ?");
    $del_stmt->execute([$id]);
    header("Location: manage_videos.php?deleted=1");
    exit();
}

// --- HANDLE CREATE ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_video'])) {
    $title = $_POST['video_title'];
    $type = $_POST['video_type'];
    $source = "";

    if ($type == 'youtube') {
        $url = $_POST['youtube_url'];
        if (strpos($url, 'watch?v=') !== false) {
            $source = str_replace('watch?v=', 'embed/', $url);
        } elseif (strpos($url, 'youtu.be/') !== false) {
            $source = str_replace('youtu.be/', 'youtube.com/embed/', $url);
        } else {
            $source = $url;
        }
    } else {
        $upload_dir = "../uploads/videos/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $file_name = time() . "_" . basename($_FILES["video_file"]["name"]);
        $upload_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES["video_file"]["tmp_name"], $upload_file)) {
            $source = "uploads/videos/" . $file_name;
        }
    }

    if (!empty($source)) {
        $stmt = $db->prepare("INSERT INTO admin_videos (video_title, video_type, video_source) VALUES (?, ?, ?)");
        $stmt->execute([$title, $type, $source]);
        header("Location: manage_videos.php?success=1");
        exit();
    }
}

// --- HANDLE UPDATE (EDIT) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_video'])) {
    $id = $_POST['video_id'];
    $title = $_POST['video_title'];
    $type = $_POST['video_type'];
    $source = $_POST['existing_source']; // Default to old source

    if ($type == 'youtube' && !empty($_POST['youtube_url'])) {
        $url = $_POST['youtube_url'];
        if (strpos($url, 'watch?v=') !== false) {
            $source = str_replace('watch?v=', 'embed/', $url);
        } elseif (strpos($url, 'youtu.be/') !== false) {
            $source = str_replace('youtu.be/', 'youtube.com/embed/', $url);
        } else {
            $source = $url;
        }
    } elseif ($type == 'raw' && !empty($_FILES["video_file"]["name"])) {
        $upload_dir = "../uploads/videos/";
        $file_name = time() . "_" . basename($_FILES["video_file"]["name"]);
        $upload_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES["video_file"]["tmp_name"], $upload_file)) {
            // Delete old file if it was a raw video
            if (strpos($_POST['existing_source'], 'uploads/videos/') !== false) {
                if (file_exists("../" . $_POST['existing_source'])) unlink("../" . $_POST['existing_source']);
            }
            $source = "uploads/videos/" . $file_name;
        }
    }

    $stmt = $db->prepare("UPDATE admin_videos SET video_title=?, video_type=?, video_source=? WHERE id=?");
    $stmt->execute([$title, $type, $source, $id]);
    header("Location: manage_videos.php?updated=1");
    exit();
}

$videos = $db->query("SELECT * FROM admin_videos ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Videos - Faculty Union</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --maroon: #8c1d1d; }
        .btn-maroon { background: var(--maroon); color: white; }
        .btn-maroon:hover { background: #6b1616; color: white; }
        .video-preview { width: 160px; height: 90px; object-fit: cover; border-radius: 6px; background: #000; overflow: hidden; }
        .back-link { text-decoration: none; color: #666; font-weight: 500; transition: 0.3s; }
        .back-link:hover { color: var(--maroon); }
        .hidden-field { display: none; }

        .content {
            /* Match sidebar width and padding for consistent layout */
            margin-left: 260px;
            padding: 40px;
            max-width: calc(100% - 260px);
        }

        @media (max-width: 991.98px) {
            .content {
                margin-left: 0;
                max-width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>
<body class="bg-light">

    <link rel="icon" href="../images/facultyunion.png">
<div class="container content mt-4 pb-5">
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Video added successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Video updated.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        Video deleted.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card shadow mb-5">
        <div class="card-header btn-maroon text-white">
            <h4 class="mb-0"><i class="fas fa-video me-2"></i> Add New Video</h4>
        </div>
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Video Title</label>
                    <input type="text" name="video_title" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Type</label>
                    <select name="video_type" class="form-select" onchange="toggleInputs(this, 'add_yt', 'add_raw')" required>
                        <option value="" selected disabled>Choose type...</option>
                        <option value="youtube">YouTube Link</option>
                        <option value="raw">Raw Video Upload</option>
                    </select>
                </div>
                <div class="col-12 hidden-field" id="add_yt">
                    <label class="form-label">YouTube URL</label>
                    <input type="url" name="youtube_url" class="form-control">
                </div>
                <div class="col-12 hidden-field" id="add_raw">
                    <label class="form-label">Upload Video File</label>
                    <input type="file" name="video_file" class="form-control" accept="video/*">
                </div>
                <div class="col-12 text-end">
                    <button type="submit" name="add_video" class="btn btn-maroon px-5">Publish</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="p-3 d-flex justify-content-between align-items-center">
            <div class="fw-semibold">All Videos</div>
            <input id="videoSearch" type="search" class="form-control form-control-sm" placeholder="Search title..." style="width:220px;">
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Preview</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($videos as $row): ?>
                    <tr>
                        <td>
                            <?php if ($row['video_type'] == 'youtube'): ?>
                                <div class="video-preview">
                                    <iframe src="<?php echo htmlspecialchars($row['video_source']); ?>" frameborder="0" allowfullscreen style="width:100%;height:100%;border:0;"></iframe>
                                </div>
                            <?php else: ?>
                                <div class="video-preview">
                                    <video src="<?php echo '../' . htmlspecialchars($row['video_source']); ?>" controls muted preload="metadata" style="width:100%;height:100%;object-fit:cover;"></video>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($row['video_title']); ?></strong></td>
                        <td><span class="badge <?php echo $row['video_type'] == 'youtube' ? 'bg-danger' : 'bg-primary'; ?>"><?php echo strtoupper($row['video_type']); ?></span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary me-2" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-header btn-maroon text-white">
                    <h5 class="modal-title">Edit Video Content</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="video_id" id="edit_id">
                    <input type="hidden" name="existing_source" id="edit_existing_source">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Video Title</label>
                            <input type="text" name="video_title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="video_type" id="edit_type" class="form-select" onchange="toggleInputs(this, 'edit_yt', 'edit_raw')" required>
                                <option value="youtube">YouTube Link</option>
                                <option value="raw">Raw Video Upload</option>
                            </select>
                        </div>
                        <div class="col-12 hidden-field" id="edit_yt">
                            <label class="form-label">YouTube URL (Leave blank to keep current)</label>
                            <input type="url" name="youtube_url" id="edit_url_val" class="form-control">
                        </div>
                        <div class="col-12 hidden-field" id="edit_raw">
                            <label class="form-label">Replace Video File (Optional)</label>
                            <input type="file" name="video_file" class="form-control" accept="video/*">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div id="edit_preview" style="width:100%;height:320px;background:#000;border-radius:6px;overflow:hidden;"></div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_video" class="btn btn-maroon">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
function toggleInputs(selectObj, ytId, rawId) {
    const yt = document.getElementById(ytId);
    const raw = document.getElementById(rawId);
    if (!selectObj) return;
    if (selectObj.value === 'youtube') {
        yt.style.display = 'block';
        raw.style.display = 'none';
    } else {
        yt.style.display = 'none';
        raw.style.display = 'block';
    }
}

function updateEditPreview() {
    const preview = document.getElementById('edit_preview');
    if (!preview) return;
    const type = document.getElementById('edit_type').value;
    const existing = document.getElementById('edit_existing_source').value || '';
    const ytInput = document.getElementById('edit_url_val').value || '';
    preview.innerHTML = '';
    if (type === 'youtube') {
        const src = ytInput || existing;
        preview.innerHTML = '<iframe src="' + src + '" frameborder="0" allowfullscreen style="width:100%;height:100%;border:0;"></iframe>';
    } else {
        const src = existing;
        preview.innerHTML = '<video src="../' + src + '" controls style="width:100%;height:100%;object-fit:cover;"></video>';
    }
}

function openEditModal(data) {
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_title').value = data.video_title;
    document.getElementById('edit_type').value = data.video_type;
    document.getElementById('edit_existing_source').value = data.video_source;
    const ytVal = document.getElementById('edit_url_val');
    if (data.video_type === 'youtube') {
        ytVal.value = data.video_source;
    } else {
        ytVal.value = '';
    }
    toggleInputs(document.getElementById('edit_type'), 'edit_yt', 'edit_raw');
    updateEditPreview();
    // attach listeners (ensure duplicates don't pile up)
    const typeEl = document.getElementById('edit_type');
    const urlEl = document.getElementById('edit_url_val');
    typeEl.onchange = function() { toggleInputs(this, 'edit_yt', 'edit_raw'); updateEditPreview(); };
    urlEl.oninput = updateEditPreview;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

// table search
const searchInput = document.getElementById('videoSearch');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const q = this.value.trim().toLowerCase();
        document.querySelectorAll('table tbody tr').forEach(row => {
            const title = row.querySelector('td:nth-child(2)').innerText.toLowerCase();
            row.style.display = title.includes(q) ? '' : 'none';
        });
    });
}
</script>

</body>
</html>