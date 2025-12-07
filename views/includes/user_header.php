<header class="header">
    <div class="header-content">
        <div class="logo" style="display: flex !important; align-items: center !important; gap: 15px !important; justify-content: flex-start !important; flex-direction: row !important;">
            <img src="<?php echo SITE_URL; ?>/assets/images/logo.png?v=<?php echo time(); ?>" alt="<?php echo SITE_NAME; ?>" class="logo-image" style="height: 60px !important; width: 60px !important; flex-shrink: 0 !important;">
            <h1 style="margin: 0 !important; font-size: 1.5rem !important; text-align: left !important;"><?php echo SITE_NAME; ?></h1>
        </div>
        <nav class="nav">
            <a href="<?php echo SITE_URL; ?>/views/user/dashboard.php">Dashboard</a>
            <a href="<?php echo SITE_URL; ?>/views/user/new_appointment.php">New Appointment</a>
            <a href="<?php echo SITE_URL; ?>/views/user/profile.php" title="Profile">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </a>
            <div class="user-menu">
                <a href="<?php echo SITE_URL; ?>/controllers/AuthController.php?action=logout" class="btn btn-sm" title="Logout">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </a>
            </div>
        </nav>
    </div>
</header>