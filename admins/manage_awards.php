<?php
// Corrected path to step out of 'admins' folder to find 'class'
require_once('../class/database.php');
require_once('sidebar.php');
$database = new Database();
$db = $database->getConnection();
$navtext = "Awards";
require_once('navbar.php');

// --- HANDLE DELETE ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM awards WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_awards.php?deleted=1");
    exit();
}

// --- HANDLE CREATE ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_award'])) {
    $title = $_POST['award_title'];
    $recipient = $_POST['recipient_name'];
    $desc = $_POST['description'];
    $year = $_POST['award_year'];
    
    $upload_dir = "../uploads/awards/"; 
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $file_name = time() . "_" . basename($_FILES["award_image"]["name"]);
    $upload_file = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES["award_image"]["tmp_name"], $upload_file)) {
        $db_save_path = "uploads/awards/" . $file_name; 
        $stmt = $db->prepare("INSERT INTO awards (award_title, recipient_name, description, award_image, award_year) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $recipient, $desc, $db_save_path, $year]);
        header("Location: manage_awards.php?success=1");
        exit();
    }
}

// --- HANDLE UPDATE (EDIT) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_award'])) {
    $id = $_POST['award_id'];
    $title = $_POST['award_title'];
    $recipient = $_POST['recipient_name'];
    $desc = $_POST['description'];
    $year = $_POST['award_year'];

    if (!empty($_FILES["award_image"]["name"])) {
        $upload_dir = "../uploads/awards/";
        $file_name = time() . "_" . basename($_FILES["award_image"]["name"]);
        $upload_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES["award_image"]["tmp_name"], $upload_file)) {
            $db_save_path = "uploads/awards/" . $file_name;
            $stmt = $db->prepare("UPDATE awards SET award_title=?, recipient_name=?, description=?, award_year=?, award_image=? WHERE id=?");
            $stmt->execute([$title, $recipient, $desc, $year, $db_save_path, $id]);
        }
    } else {
        $stmt = $db->prepare("UPDATE awards SET award_title=?, recipient_name=?, description=?, award_year=? WHERE id=?");
        $stmt->execute([$title, $recipient, $desc, $year, $id]);
    }
    header("Location: manage_awards.php?updated=1");
    exit();
}

$awards = $db->query("SELECT * FROM awards ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Flash message handling
$message = '';
$message_type = 'info';
if (isset($_GET['success'])) { $message = 'Award published.'; $message_type = 'success'; }
if (isset($_GET['updated'])) { $message = 'Award updated.'; $message_type = 'success'; }
if (isset($_GET['deleted'])) { $message = 'Award deleted.'; $message_type = 'warning'; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Awards - Faculty Union</title>
    <link rel="icon" href="../images/facultyunion.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --maroon: #8c1d1d; }
        .btn-maroon { background: var(--maroon); color: white; }
        .btn-maroon:hover { background: #6b1616; color: white; }
        .img-preview { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd; }
        .back-link { text-decoration: none; color: #666; font-weight: 500; transition: 0.3s; }
        .back-link:hover { color: var(--maroon); }

        .content {
            /* Match the sidebar width so the sidebar is visible */
            margin-left: 260px; /* should match .sidebar width in sidebar.php */
            padding: 40px; /* match .main-content padding from sidebar.php */
            max-width: calc(100% - 260px);
        }

        /* Collapse sidebar layout at the same breakpoint used in sidebar.php */
        @media (max-width: 991.98px) {
            .content {
                margin-left: 0;
                max-width: 100%;
                padding: 20px;
            }
        }

        /* UI tweaks */
        .card-header.btn-maroon { color: #fff; }
        .table td { vertical-align: middle; }
        .award-desc { max-width: 420px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .img-large { width: 100px; height: 80px; object-fit: cover; border-radius: 6px; }
        .file-preview { display:inline-block; width:80px; height:80px; object-fit:cover; border-radius:6px; border:1px solid #e0e0e0; }
        .btn-sm .bi { margin-right:6px; }
    </style>
</head>
<body class="bg-light">

<div class="container content mt-2 pb-5">

    <?php if(!empty($message)): ?>
        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>


    <div class="card shadow mb-5">
        <div class="card-header btn-maroon">
            <h4 class="mb-0">Add New Faculty Award</h4>
        </div>
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Award Title</label>
                    <input type="text" name="award_title" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Recipient Name</label>
                    <input type="text" name="recipient_name" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Award Year</label>
                    <input type="number" name="award_year" class="form-control" value="<?php echo date('Y'); ?>" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Award Image</label>
                    <input type="file" id="add_award_image" name="award_image" class="form-control" accept="image/*" required>
                    <div class="mt-2">
                        <img id="add_preview" src="#" alt="Preview" class="file-preview d-none">
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Description / Citation</label>
                    <textarea name="description" class="form-control" rows="3" required></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" name="add_award" class="btn btn-maroon w-100">Publish Award</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-dark">Existing Awards</h4>
            <span class="badge bg-secondary"><?php echo count($awards); ?> Total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Award Details</th>
                        <th>Year</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($awards)): foreach ($awards as $row): ?>
                    <tr>
                        <td><img src="../<?php echo $row['award_image']; ?>" class="img-preview"></td>
                        <td>
                            <strong class="text-dark"><?php echo htmlspecialchars($row['award_title']); ?></strong><br>
                            <small class="text-muted">Recipient: <?php echo htmlspecialchars($row['recipient_name']); ?></small>
                        </td>
                        <td><span class="badge bg-light text-dark border"><?php echo $row['award_year']; ?></span></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="4" class="text-center py-4 text-muted">No awards found in database.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="editAwardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-header btn-maroon text-white">
                    <h5 class="modal-title">Edit Award Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="award_id" id="edit_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Award Title</label>
                            <input type="text" name="award_title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Recipient Name</label>
                            <input type="text" name="recipient_name" id="edit_recipient" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Award Year</label>
                            <input type="number" name="award_year" id="edit_year" class="form-control" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Update Image (Leave blank to keep current)</label>
                            <input type="file" id="edit_award_image" name="award_image" class="form-control" accept="image/*">
                            <div class="mt-2 d-flex align-items-center gap-3">
                                <img id="current_image" src="#" alt="Current" class="img-large d-none">
                                <span class="text-muted small">Leave blank to keep current image</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description / Citation</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_award" class="btn btn-maroon">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
function openEditModal(award) {
    document.getElementById('edit_id').value = award.id;
    document.getElementById('edit_title').value = award.award_title;
    document.getElementById('edit_recipient').value = award.recipient_name;
    document.getElementById('edit_year').value = award.award_year;
    document.getElementById('edit_description').value = award.description;

    // current image
    var curImg = document.getElementById('current_image');
    if (award.award_image) {
        curImg.src = '../' + award.award_image;
        curImg.classList.remove('d-none');
    } else {
        curImg.classList.add('d-none');
    }

    // clear edit file input preview
    var editFile = document.getElementById('edit_award_image');
    if (editFile) editFile.value = '';

    var editModal = new bootstrap.Modal(document.getElementById('editAwardModal'));
    editModal.show();
}

function previewFile(inputEl, previewId) {
    var file = inputEl.files && inputEl.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        var img = document.getElementById(previewId);
        img.src = e.target.result;
        img.classList.remove('d-none');
    }
    reader.readAsDataURL(file);
}

document.addEventListener('DOMContentLoaded', function(){
    var addInput = document.getElementById('add_award_image');
    if (addInput) addInput.addEventListener('change', function(){ previewFile(this, 'add_preview'); });

    var editInput = document.getElementById('edit_award_image');
    if (editInput) editInput.addEventListener('change', function(){ previewFile(this, 'current_image'); });
});
</script>

</body>
</html>