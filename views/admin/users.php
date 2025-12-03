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
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
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

        .badge-staff {
            background-color: #3498db;
            color: white;
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
            background-color: #3498db;
            color: white;
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
    </style>
</head>

<body>
    <?php include __DIR__ . '/../includes/admin_header.php'; ?>

    <div class="main-container">
        <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

        <main class="content">
            <div class="page-header">
                <h1>Manage Users</h1>
                <button onclick="openUserModal()" class="btn btn-primary">+ Add New User</button>
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

            <div class="table-container">
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