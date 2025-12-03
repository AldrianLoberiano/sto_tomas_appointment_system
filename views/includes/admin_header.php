<header class="header">
    <div class="header-content">
        <div class="logo">
            <h1><?php echo SITE_NAME; ?></h1>
        </div>
        <nav class="nav">
            <a href="<?php echo SITE_URL; ?>/views/admin/dashboard.php">Dashboard</a>
            <a href="<?php echo SITE_URL; ?>/views/admin/appointments.php">Appointments</a>
            <a href="<?php echo SITE_URL; ?>/views/admin/services.php">Services</a>
            <a href="<?php echo SITE_URL; ?>/views/admin/users.php">Users</a>
            <div class="user-menu">
                <span><?php echo $_SESSION['full_name']; ?></span>
                <a href="<?php echo SITE_URL; ?>/controllers/AuthController.php?action=logout" class="btn btn-sm">Logout</a>
            </div>
        </nav>
    </div>
</header>