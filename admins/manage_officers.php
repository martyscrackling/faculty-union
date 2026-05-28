<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: auth/login.php"); exit(); }
require_once('../class/database.php');
require_once('sidebar.php');
$database = new Database();
$db = $database->getConnection();
$navtext = "Officers";
require_once('navbar.php');

$msg = "";

function deleteOfficerProfilePicture($relativePath) {
    if (empty($relativePath)) {
        return;
    }

    $absolutePath = dirname(__DIR__) . '/' . ltrim(str_replace('\\', '/', $relativePath), '/');
    if (is_file($absolutePath)) {
        unlink($absolutePath);
    }
}

function saveOfficerProfilePicture(array $file, $currentPath = '') {
    if (empty($file['name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return $currentPath;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return $currentPath;
    }

    if (@getimagesize($file['tmp_name']) === false) {
        return $currentPath;
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($extension, $allowedExtensions, true)) {
        return $currentPath;
    }

    $uploadDir = __DIR__ . '/uploads/awards/officers/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = 'officer_' . time() . '_' . uniqid() . '.' . $extension;
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        if (!empty($currentPath)) {
            deleteOfficerProfilePicture($currentPath);
        }

        return 'admins/uploads/awards/officers/' . $fileName;
    }

    return $currentPath;
}

$hasOfficerPhotoColumn = false;
try {
    $columnCheck = $db->query("SHOW COLUMNS FROM officers LIKE 'profile_picture'");
    $hasOfficerPhotoColumn = $columnCheck && $columnCheck->rowCount() > 0;

    if (!$hasOfficerPhotoColumn) {
        $db->exec("ALTER TABLE officers ADD profile_picture VARCHAR(255) DEFAULT NULL AFTER rank");
        $hasOfficerPhotoColumn = true;
    }
} catch (PDOException $e) {
    $hasOfficerPhotoColumn = false;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($hasOfficerPhotoColumn) {
        $photoStmt = $db->prepare("SELECT profile_picture FROM officers WHERE id = ?");
        $photoStmt->execute([$id]);
        $photoPath = $photoStmt->fetchColumn();
        deleteOfficerProfilePicture($photoPath);
    }
    $stmt = $db->prepare("DELETE FROM officers WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_officers.php?msg=Deleted");
    exit();
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_officer'])) {
    $name = $_POST['full_name'];
    $pos = $_POST['position'];
    $dept = $_POST['department'];
    $cat = $_POST['category'];
    $rank = $_POST['rank'];
    $photoPath = $hasOfficerPhotoColumn ? saveOfficerProfilePicture($_FILES['profile_picture'] ?? [], '') : null;

    if ($hasOfficerPhotoColumn) {
        $stmt = $db->prepare("INSERT INTO officers (full_name, position, department_acronym, category, rank, profile_picture) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $pos, $dept, $cat, $rank, $photoPath]);
    } else {
        $stmt = $db->prepare("INSERT INTO officers (full_name, position, department_acronym, category, rank) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $pos, $dept, $cat, $rank]);
    }
    header("Location: manage_officers.php?msg=Added");
    exit();
}

// --- FIX: Handle Edit Logic ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_officer'])) {
    $id = $_POST['id'];
    $name = $_POST['full_name'];
    $pos = $_POST['position'];
    $dept = $_POST['department'];
    $cat = $_POST['category'];
    $rank = $_POST['rank'];
    $currentPhoto = $_POST['current_profile_picture'] ?? '';
    $photoPath = $hasOfficerPhotoColumn ? saveOfficerProfilePicture($_FILES['profile_picture'] ?? [], $currentPhoto) : null;

    if ($hasOfficerPhotoColumn) {
        $stmt = $db->prepare("UPDATE officers SET full_name = ?, position = ?, department_acronym = ?, category = ?, rank = ?, profile_picture = ? WHERE id = ?");
        $stmt->execute([$name, $pos, $dept, $cat, $rank, $photoPath, $id]);
    } else {
        $stmt = $db->prepare("UPDATE officers SET full_name = ?, position = ?, department_acronym = ?, category = ?, rank = ? WHERE id = ?");
        $stmt->execute([$name, $pos, $dept, $cat, $rank, $id]);
    }
    header("Location: manage_officers.php?msg=Updated");
    exit();
}

$officers = $db->query("SELECT * FROM officers ORDER BY rank ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Officers - Admin</title>
    <link rel="stylesheet" href="../vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../vendor/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .btn-maroon{background:#800000;color:#fff;border:none}
        .btn-maroon:hover{background:#5c0000;color:#fff}
        .table img{width:56px;height:56px;object-fit:cover}
        .modal-body .form-control-file{padding:.25rem 0}
        .profile-preview{width:72px;height:72px;object-fit:cover}
    </style>
</head>
<body class="bg-light">

    <link rel="icon" href="../images/facultyunion.png">


<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb-4 align-items-center">
            <button class="btn btn-maroon" data-toggle="modal" data-target="#addModal">Add New Officer</button>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle text-center">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Rank</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Dept</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($officers as $o): ?>
                    <tr>
                        <td style="width: 84px;">
                            <?php $photoSrc = !empty($o['profile_picture']) ? '../' . $o['profile_picture'] : '../images/facultyunion.png'; ?>
                            <img src="<?php echo htmlspecialchars($photoSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($o['full_name'], ENT_QUOTES, 'UTF-8'); ?>" class="rounded-circle border" style="width:56px;height:56px;object-fit:cover;">
                        </td>
                        <td><?php echo htmlspecialchars($o['rank'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($o['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($o['position'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($o['department_acronym'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><span class="badge badge-secondary"><?php echo htmlspecialchars($o['category'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td class="text-nowrap">
                                <button class="btn btn-sm btn-info edit-btn" 
                                    data-id="<?php echo $o['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($o['full_name']); ?>"
                                    data-pos="<?php echo htmlspecialchars($o['position']); ?>"
                                    data-dept="<?php echo htmlspecialchars($o['department_acronym']); ?>"
                                    data-cat="<?php echo $o['category']; ?>"
                                    data-rank="<?php echo $o['rank']; ?>"
                                    data-photo="<?php echo htmlspecialchars($o['profile_picture'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    data-toggle="modal" data-target="#editModal">Edit</button>

                            <a href="?delete=<?php echo $o['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this officer?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" enctype="multipart/form-data">
            <div class="modal-header"><h5>Add New Officer</h5></div>
            <div class="modal-body">
                <input type="text" name="full_name" class="form-control mb-2" placeholder="Full Name" required>
                <input type="text" name="position" class="form-control mb-2" placeholder="Position" required>
                <input type="text" name="department" class="form-control mb-2" placeholder="Dept Acronym" required>
                <select name="category" class="form-control mb-2">
                    <option value="Executive">Executive</option>
                    <option value="Finance">Finance</option>
                </select>
                <input type="number" name="rank" class="form-control mb-2" placeholder="Rank (Order)" required>
                <label class="small font-weight-bold mt-2">Profile Picture</label>
                <input type="file" name="profile_picture" class="form-control-file" accept="image/*">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="add_officer" class="btn btn-maroon">Save Officer</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" enctype="multipart/form-data">
            <div class="modal-header"><h5>Edit Officer</h5></div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="current_profile_picture" id="current_profile_picture">
                <label class="small font-weight-bold">Full Name</label>
                <input type="text" name="full_name" id="edit_name" class="form-control mb-2" required>
                
                <label class="small font-weight-bold">Position</label>
                <input type="text" name="position" id="edit_pos" class="form-control mb-2" required>
                
                <label class="small font-weight-bold">Department Acronym</label>
                <input type="text" name="department" id="edit_dept" class="form-control mb-2" required>
                
                <label class="small font-weight-bold">Category</label>
                <select name="category" id="edit_cat" class="form-control mb-2">
                    <option value="Executive">Executive</option>
                    <option value="Finance">Finance</option>
                </select>
                
                <label class="small font-weight-bold">Rank</label>
                <input type="number" name="rank" id="edit_rank" class="form-control mb-2" required>

                <label class="small font-weight-bold mt-2">Profile Picture</label>
                <input type="file" name="profile_picture" class="form-control-file" accept="image/*">
                <div class="mt-3">
                    <img id="edit_photo_preview" src="../images/facultyunion.png" alt="Current profile picture" class="rounded-circle border" style="width:72px;height:72px;object-fit:cover;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" name="edit_officer" class="btn btn-maroon">Update Changes</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Script to fill the Edit Modal with existing data
$('.edit-btn').on('click', function() {
    $('#edit_id').val($(this).data('id'));
    $('#edit_name').val($(this).data('name'));
    $('#edit_pos').val($(this).data('pos'));
    $('#edit_dept').val($(this).data('dept'));
    $('#edit_cat').val($(this).data('cat'));
    $('#edit_rank').val($(this).data('rank'));
    $('#current_profile_picture').val($(this).data('photo') || '');
    const photo = $(this).data('photo');
    $('#edit_photo_preview').attr('src', photo ? '../' + photo : '../images/facultyunion.png');
});
    // Preview selected image in edit modal
    $('#editModal input[name="profile_picture"]').on('change', function(e){
        const file = this.files && this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(ev){ $('#edit_photo_preview').attr('src', ev.target.result); };
        reader.readAsDataURL(file);
    });
</script>

</body>
</html>