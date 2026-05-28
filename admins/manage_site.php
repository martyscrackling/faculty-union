<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { 
    header("Location: ../auth/login.php"); exit(); 
}
require_once('../class/database.php');
require_once('sidebar.php');
$database = new Database();
$db = $database->getConnection();
$navtext = "Site Identity";
require_once('navbar.php');

$success = "";
$error = "";
$settings = [
    'site_name' => 'Faculty Union',
    'logo_path' => '../images/facultyunion.png'
];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $site_name = trim($_POST['site_name']);
    $logo_path = $_POST['current_logo']; // Keep old logo by default

    // Handle File Upload if a new logo is selected
    if (!empty($_FILES['logo']['name'])) {
        $target_dir = "../img/";
        $file_name = time() . "_" . basename($_FILES["logo"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
            $logo_path = "img/" . $file_name; // Store relative path for the frontend
        } else {
            $error = "Failed to upload logo.";
        }
    }

    if (empty($error)) {
        try {
            if ($db instanceof PDO) {
                $stmt = $db->prepare("UPDATE site_settings SET site_name = ?, logo_path = ? WHERE id = 1");
                $stmt->execute([$site_name, $logo_path]);
                $success = "Site settings updated successfully!";
                $settings['site_name'] = $site_name;
                $settings['logo_path'] = $logo_path;
            } else {
                $error = "Database connection unavailable.";
            }
        } catch (PDOException $exception) {
            $error = "Unable to update site settings.";
        }
    }
}

// Fetch current settings
if ($db instanceof PDO) {
    try {
        $settings_query = $db->query("SELECT * FROM site_settings WHERE id = 1");
        $fetched_settings = $settings_query ? $settings_query->fetch(PDO::FETCH_ASSOC) : false;

        if (is_array($fetched_settings)) {
            $settings = array_merge($settings, $fetched_settings);
        }
    } catch (PDOException $exception) {
        $error = $error ?: "Site settings are not available yet.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manage Site Identity</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; }
        .card { border-top: 6px solid #8c1d1d; border-radius: .5rem; }
        .btn-maroon { background: #8c1d1d; color: #fff; }
        .logo-preview { max-height: 120px; object-fit: contain; }
        .form-label { font-weight: 600; }
    </style>
</head>
<body>
<div class="container py-5">
    <link rel="icon" href="../images/facultyunion.png">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php if($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="current_logo" value="<?php echo htmlspecialchars($settings['logo_path']); ?>">

                        <div class="form-group">
                            <label class="form-label">Site Name</label>
                            <input type="text" name="site_name" class="form-control" value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Current Logo</label>
                            <div class="d-flex align-items-center mb-2">
                                <img id="logoPreview" src="../<?php echo htmlspecialchars($settings['logo_path']); ?>" class="logo-preview img-fluid border p-2 mr-3" alt="logo">
                                <div class="small text-muted">Recommended: PNG or JPG. Leave empty to keep current logo.</div>
                            </div>

                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="logo" name="logo" accept="image/*">
                                <label class="custom-file-label" for="logo">Choose file</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-maroon btn-block mt-3">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var input = document.getElementById('logo');
    var label = document.querySelector('label[for="logo"]');
    var preview = document.getElementById('logoPreview');

    if(input){
        input.addEventListener('change', function(e){
            var file = this.files[0];
            if(file){
                label.textContent = file.name;
                var reader = new FileReader();
                reader.onload = function(ev){ preview.src = ev.target.result; };
                reader.readAsDataURL(file);
            } else {
                label.textContent = 'Choose file';
                // restore original preview if available
                preview.src = '../' + '<?php echo addslashes($settings['logo_path']); ?>';
            }
        });
    }
});
</script>
</body>
</html>