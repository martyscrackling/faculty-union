<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: ../auth/login.php"); exit(); }
require_once('../class/database.php');
require_once('sidebar.php');
$database = new Database();
$db = $database->getConnection();
$navtext = "Contact Information";
require_once('navbar.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $db->prepare("UPDATE contact_info SET address=?, phone=?, hours=?, email=?, facebook_url=?, facebook_name=? WHERE id=1");
    $stmt->execute([$_POST['address'], $_POST['phone'], $_POST['hours'], $_POST['email'], $_POST['facebook_url'], $_POST['facebook_name']]);
    $success = "Contact info updated!";
}

$info = $db->query("SELECT * FROM contact_info WHERE id=1")->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Contact Info</title>
    <link rel="icon" href="../images/facultyunion.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --maroon: #8c1d1d; }
        body { background: #f7f7f9; }
        .btn-maroon { background: var(--maroon); color: #fff; }
        .card-custom { border-top: 6px solid var(--maroon); max-width:760px; margin:auto; }
        .form-label { font-weight:600; font-size:0.95rem; }
        .input-group-text { background: transparent; border-right:0; }
        .form-control { border-left:0; }
        .mb-1 { margin-bottom:.5rem; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="card p-4 shadow-sm card-custom">
            <div class="d-flex align-items-center mb-3">
                <div class="mr-3"><i class="bi bi-telephone-fill" style="font-size:1.6rem;color:var(--maroon)"></i></div>
                <h4 class="mb-0">Manage Contact Details</h4>
            </div>
            <?php if(isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($success, ENT_QUOTES); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group mb-3">
                    <label class="form-label">Address</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span></div>
                        <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($info['address'] ?? '', ENT_QUOTES); ?></textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="bi bi-telephone"></i></span></div>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($info['phone'] ?? '', ENT_QUOTES); ?>">
                        </div>
                    </div>
                    <div class="form-group col-md-6 mb-3">
                        <label class="form-label">Office Hours</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="bi bi-clock"></i></span></div>
                            <input type="text" name="hours" class="form-control" value="<?php echo htmlspecialchars($info['hours'] ?? '', ENT_QUOTES); ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="bi bi-envelope-fill"></i></span></div>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($info['email'] ?? '', ENT_QUOTES); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6 mb-3">
                        <label class="form-label">FB Page Name</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="bi bi-facebook"></i></span></div>
                            <input type="text" name="facebook_name" class="form-control" value="<?php echo htmlspecialchars($info['facebook_name'] ?? '', ENT_QUOTES); ?>">
                        </div>
                    </div>
                    <div class="form-group col-md-6 mb-3">
                        <label class="form-label">FB URL</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="bi bi-link-45deg"></i></span></div>
                            <input type="text" name="facebook_url" class="form-control" value="<?php echo htmlspecialchars($info['facebook_url'] ?? '', ENT_QUOTES); ?>">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-maroon btn-block btn-lg">Update</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>