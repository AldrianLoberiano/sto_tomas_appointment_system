<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

AuthController::requireLogin();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Get current user details
$user->id = $_SESSION['user_id'];
$user->readOne();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .profile-header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: center;
        }

        .profile-avatar-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #3498db;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            font-weight: bold;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .change-picture-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #3498db;
            color: white;
            border: 3px solid white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: background 0.3s;
        }

        .change-picture-btn:hover {
            background: #2980b9;
        }

        .profile-name {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .profile-email {
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .profile-role {
            display: inline-block;
            padding: 5px 15px;
            background: #3498db;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            text-transform: uppercase;
        }

        .profile-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .profile-section h2 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .info-label {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 500;
        }

        .edit-mode {
            display: none;
        }

        .edit-mode.active {
            display: block;
        }

        .view-mode.hidden {
            display: none;
        }

        #pictureInput {
            display: none;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            margin: 0;
        }

        .close-modal {
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
            border: none;
            background: none;
        }

        .close-modal:hover {
            color: #000;
        }

        .preview-container {
            text-align: center;
            margin: 20px 0;
        }

        .preview-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #3498db;
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../includes/user_header.php'; ?>

    <div class="main-container">
        <main class="content">
            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-avatar-wrapper">
                        <div class="profile-avatar" id="avatarDisplay">
                            <?php if (!empty($user->profile_picture) && file_exists(__DIR__ . '/../../' . $user->profile_picture)): ?>
                                <img src="<?php echo SITE_URL . '/' . $user->profile_picture; ?>?v=<?php echo time(); ?>" alt="Profile Picture">
                            <?php else: ?>
                                <?php echo strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                        <button class="change-picture-btn" onclick="openPictureModal()" title="Change Picture">📷</button>
                    </div>
                    <div class="profile-name">
                        <?php echo htmlspecialchars($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name); ?>
                    </div>
                    <div class="profile-email">
                        <?php echo htmlspecialchars($user->email); ?>
                    </div>
                    <span class="profile-role"><?php echo ucfirst($user->role); ?></span>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>

                <!-- View Mode -->
                <div class="profile-section view-mode" id="viewMode">
                    <h2>Personal Information</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Username</div>
                            <div class="info-value"><?php echo htmlspecialchars($user->username); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?php echo htmlspecialchars($user->email); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">First Name</div>
                            <div class="info-value"><?php echo htmlspecialchars($user->first_name); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Last Name</div>
                            <div class="info-value"><?php echo htmlspecialchars($user->last_name); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Middle Name</div>
                            <div class="info-value"><?php echo htmlspecialchars($user->middle_name ?: 'N/A'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Phone</div>
                            <div class="info-value"><?php echo htmlspecialchars($user->phone); ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($user->address); ?></div>
                    </div>
                    <div style="margin-top: 20px;">
                        <button onclick="toggleEditMode()" class="btn btn-primary">Edit Profile</button>
                    </div>
                </div>

                <!-- Edit Mode -->
                <div class="profile-section edit-mode" id="editMode">
                    <h2>Edit Profile</h2>
                    <form method="POST" action="<?php echo SITE_URL; ?>/controllers/ProfileController.php">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="user_id" value="<?php echo $user->id; ?>">

                        <div class="form-row">
                            <div class="form-group">
                                <label for="username">Username *</label>
                                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user->username); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user->email); ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name *</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user->first_name); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user->last_name); ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="middle_name">Middle Name</label>
                                <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($user->middle_name); ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone *</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user->phone); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Address *</label>
                            <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($user->address); ?></textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <button type="button" onclick="toggleEditMode()" class="btn">Cancel</button>
                        </div>
                    </form>
                </div>

                <!-- Change Password Section -->
                <div class="profile-section">
                    <h2>Change Password</h2>
                    <form method="POST" action="<?php echo SITE_URL; ?>/controllers/ProfileController.php">
                        <input type="hidden" name="action" value="change_password">
                        <input type="hidden" name="user_id" value="<?php echo $user->id; ?>">

                        <div class="form-group">
                            <label for="current_password">Current Password *</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="new_password">New Password *</label>
                                <input type="password" id="new_password" name="new_password" required minlength="6">
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password *</label>
                                <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Change Picture Modal -->
    <div id="pictureModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Change Profile Picture</h3>
                <button class="close-modal" onclick="closePictureModal()">&times;</button>
            </div>
            <form method="POST" action="<?php echo SITE_URL; ?>/controllers/ProfileController.php" enctype="multipart/form-data" id="pictureForm">
                <input type="hidden" name="action" value="upload_picture">
                <input type="hidden" name="user_id" value="<?php echo $user->id; ?>">

                <div class="preview-container">
                    <img id="previewImage" class="preview-image" src="" alt="Preview" style="display:none;">
                </div>

                <div class="form-group">
                    <label for="pictureInput" style="cursor: pointer; display: block; text-align: center; padding: 20px; border: 2px dashed #3498db; border-radius: 5px; background: #f8f9fa;">
                        📸 Click to select image
                    </label>
                    <input type="file" id="pictureInput" name="profile_picture" accept="image/*" required>
                    <small style="display: block; text-align: center; margin-top: 10px;">Accepted: JPG, PNG (Max 2MB)</small>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Upload Picture</button>
                    <button type="button" onclick="closePictureModal()" class="btn" style="width: 100%; margin-top: 10px;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleEditMode() {
            const viewMode = document.getElementById('viewMode');
            const editMode = document.getElementById('editMode');

            viewMode.classList.toggle('hidden');
            editMode.classList.toggle('active');
        }

        function openPictureModal() {
            document.getElementById('pictureModal').classList.add('show');
        }

        function closePictureModal() {
            document.getElementById('pictureModal').classList.remove('show');
            document.getElementById('pictureInput').value = '';
            document.getElementById('previewImage').style.display = 'none';
        }

        // Preview image before upload
        document.getElementById('pictureInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('previewImage');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('pictureModal');
            if (event.target === modal) {
                closePictureModal();
            }
        };
    </script>
</body>

</html>