<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

AuthController::requireLogin();
AuthController::requireAdmin();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Get all users
$users = $user->read();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 2% auto;
            padding: 30px;
            border: 1px solid #888;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-height: 90vh;
            overflow-y: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 20px;
        }

        .close:hover,
        .close:focus {
            color: #000;
        }

        .modal h2 {
            margin-top: 0;
            color: #2c3e50;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-admin {
            background-color: #e74c3c;
            color: white;
        }

        .badge-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(79, 172, 254, 0.3);
        }

        .badge-resident {
            background-color: #95a5a6;
            color: white;
        }

        .badge-active {
            background-color: #27ae60;
            color: white;
        }

        .badge-inactive {
            background-color: #e74c3c;
            color: white;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
            margin-right: 5px;
        }

        .btn-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(79, 172, 254, 0.3);
        }

        .btn-warning {
            background-color: #f39c12;
            color: white;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        /* Hover Effects */
        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-sm:hover {
            transform: translateY(-2px) scale(1.05);
        }

        /* Table row hover */
        .table-container table tbody tr {
            transition: all 0.2s ease;
        }

        .table-container table tbody tr:hover {
            background: #ecf0f1;
            transform: translateX(3px);
            cursor: pointer;
        }

        /* Badge hover */
        .badge {
            transition: all 0.2s ease;
        }

        .badge:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        /* Modal close button hover */
        .close {
            transition: all 0.2s ease;
        }

        .close:hover {
            transform: rotate(90deg);
            color: #e74c3c;
        }

        /* Input focus enhancement */
        input:hover,
        select:hover,
        textarea:hover {
            border-color: #95a5a6;
        }

        /* Modal content hover */
        .modal-content {
            transition: all 0.3s ease;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../includes/admin_header.php'; ?>

    <div class="main-container">
        <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

        <main class="content" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; padding: 25px;">
            <div class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 35px 30px; border-radius: 20px; margin-bottom: 30px; box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3); position: relative; overflow: hidden; display: flex; justify-content: space-between; align-items: center;">
                <div style="position: absolute; top: -80px; right: -80px; width: 250px; height: 250px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -40px; left: -40px; width: 180px; height: 180px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <div style="position: relative; z-index: 1;">
                    <h1 style="color: white; font-size: 2.5rem; margin: 0 0 10px 0; text-shadow: 0 2px 10px rgba(0,0,0,0.2); font-weight: 700;">👥 User Management</h1>
                    <p style="color: rgba(255,255,255,0.95); font-size: 1.15rem; margin: 0;">Manage system users and residents</p>
                </div>
                <button onclick="openUserModal()" class="btn btn-primary" style="background: white; color: #667eea; border: none; padding: 14px 28px; border-radius: 50px; font-size: 1rem; font-weight: 600; box-shadow: 0 6px 20px rgba(0,0,0,0.15); position: relative; z-index: 1;">+ Add New User</button>
            </div>

            <?php
            $users_stats_query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admins,
                SUM(CASE WHEN role = 'user' THEN 1 ELSE 0 END) as residents
                FROM users";
            $users_stats_stmt = $db->prepare($users_stats_query);
            $users_stats_stmt->execute();
            $users_stats = $users_stats_stmt->fetch(PDO::FETCH_ASSOC);
            ?>

            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin-bottom: 35px;">
                <div class="stat-card" style="background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #3498db; position: relative; overflow: hidden; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, rgba(52,152,219,0.1) 0%, rgba(52,152,219,0.05) 100%); margin-bottom: 15px;">
                        <span style="font-size: 2rem;">👥</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Total Users</p>
                    <h3 style="font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; color: #3498db; line-height: 1;"><?php echo $users_stats['total']; ?></h3>
                    <div style="font-size: 0.85rem; color: #95a5a6; font-weight: 500;">All accounts</div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, #ffffff 0%, #fff0f0 100%); border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #e74c3c; position: relative; overflow: hidden; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, rgba(231,76,60,0.15) 0%, rgba(231,76,60,0.05) 100%); margin-bottom: 15px;">
                        <span style="font-size: 2rem;">👑</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Administrators</p>
                    <h3 style="font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; color: #e74c3c; line-height: 1;"><?php echo $users_stats['admins']; ?></h3>
                    <div style="font-size: 0.85rem; color: #95a5a6; font-weight: 500;">System admins</div>
                </div>

                <div class="stat-card" style="background: linear-gradient(135deg, #ffffff 0%, #f8f3fb 100%); border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #9b59b6; position: relative; overflow: hidden; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, rgba(155,89,182,0.15) 0%, rgba(155,89,182,0.05) 100%); margin-bottom: 15px;">
                        <span style="font-size: 2rem;">🏘️</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Residents</p>
                    <h3 style="font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; color: #9b59b6; line-height: 1;"><?php echo $users_stats['residents']; ?></h3>
                    <div style="font-size: 0.85rem; color: #95a5a6; font-weight: 500;">Barangay residents</div>
                </div>
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

            <div style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 8px 30px rgba(0,0,0,0.08);">
                <h2 style="color: #2d3748; font-size: 1.8rem; margin: 0 0 25px 0; display: flex; align-items: center; gap: 12px; font-weight: 700;">
                    <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 6px; height: 35px; border-radius: 3px;"></span>
                    All Users
                </h2>
                <div class="table-container" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['username'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone'] ?? ''); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $row['role']; ?>">
                                            <?php echo ucfirst($row['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $row['is_active'] ? 'active' : 'inactive'; ?>">
                                            <?php echo $row['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <button onclick='editUser(<?php echo json_encode($row); ?>)' class="btn btn-sm btn-info">Edit</button>
                                        <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                            <form method="POST" action="<?php echo SITE_URL; ?>/controllers/UserController.php" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUserModal()">&times;</span>
            <h2 id="modalTitle">Add New User</h2>
            <form method="POST" action="<?php echo SITE_URL; ?>/controllers/UserController.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="user_id" id="userId" value="">

                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username *</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone *</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address *</label>
                    <textarea id="address" name="address" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="role">Role *</label>
                        <select id="role" name="role" required>
                            <option value="resident">Resident</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="is_active">Status *</label>
                        <select id="is_active" name="is_active" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="passwordGroup">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password">
                    <small style="color: #7f8c8d;">Leave blank to keep current password (when editing)</small>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Save User</button>
                    <button type="button" class="btn" onclick="closeUserModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUserModal() {
            document.getElementById('userModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Add New User';
            document.getElementById('formAction').value = 'create';
            document.getElementById('userId').value = '';
            document.getElementById('password').required = true;
            document.querySelector('form').reset();
        }

        function closeUserModal() {
            document.getElementById('userModal').style.display = 'none';
        }

        function editUser(user) {
            document.getElementById('userModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('formAction').value = 'update';
            document.getElementById('userId').value = user.id;
            document.getElementById('username').value = user.username;
            document.getElementById('email').value = user.email;
            document.getElementById('first_name').value = user.first_name;
            document.getElementById('last_name').value = user.last_name;
            document.getElementById('middle_name').value = user.middle_name || '';
            document.getElementById('phone').value = user.phone;
            document.getElementById('address').value = user.address;
            document.getElementById('role').value = user.role;
            document.getElementById('is_active').value = user.is_active;
            document.getElementById('password').required = false;
            document.getElementById('password').value = '';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('userModal');
            if (event.target == modal) {
                closeUserModal();
            }
        }
    </script>
    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
</body>

</html>