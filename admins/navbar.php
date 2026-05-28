 <link rel="stylesheet" href="../vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../vendor/bootstrap-icons/bootstrap-icons.css">
<link rel="icon" href="../images/facultyunion.png">

<style>
    .navbar-custom {
        margin-top: 10;
        position: sticky;
        top: 0;
    
    }
    .title{
        margin-left: 300px;  
        margin-top: 20px ;
    }
    .navtext{
        font-weight: 700;
    }
</style>

<div class="navbar-custom">
    <header class="px-1">
        <div class="container-fluid d-flex justify-content-between align-items-center position-relative">
            <button id="menu-toggle" class="btn d-lg-none">
                <i class="bi bi-list fs-2"></i>
            </button>

            <!-- Center container with flex-grow to take available space -->
            <div class="title">
                <h1 class="navtext d-none d-lg-block m-0"><?php echo $navtext ?></h1>
            </div>

            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-end m-lg-2">
                <a href="../auth/logout.php" class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none text-dark">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                    <span class="fw-semibold d-none d-lg-inline">Logout</span>
                </a>
            </div>
        </div>
    </header>
</div>
        