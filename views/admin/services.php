<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

AuthController::requireLogin();
AuthController::requireAdmin();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Service.php';

$database = new Database();
$db = $database->getConnection();
$service = new Service($db);
$services = $service->read();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/../includes/admin_header.php'; ?>

    <div class="main-container">
        <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

        <main class="content">
            <div class="page-header">
                <h1>Services Management</h1>
                <p>Manage barangay services and offerings</p>
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

            <div class="action-buttons">
                <button onclick="openServiceModal('create')" class="btn btn-primary">➕ Add New Service</button>
            </div>

            <div class="section">
                <h2>All Services</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Service Name</th>
                                <th>Description</th>
                                <th>Fee</th>
                                <th>Processing Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $services->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['service_name']; ?></td>
                                    <td><?php echo substr($row['description'], 0, 50) . '...'; ?></td>
                                    <td>₱<?php echo number_format($row['fee'], 2); ?></td>
                                    <td><?php echo $row['processing_time']; ?></td>
                                    <td>
                                        <span class="badge <?php echo $row['is_active'] ? 'badge-completed' : 'badge-cancelled'; ?>">
                                            <?php echo $row['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button onclick='editService(<?php echo json_encode($row); ?>)' class="btn btn-sm btn-info">Edit</button>
                                        <form action="<?php echo SITE_URL; ?>/controllers/ServiceController.php?action=delete" method="POST" style="display:inline;">
                                            <input type="hidden" name="service_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this service?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Service Modal -->
    <div id="serviceModal" class="modal">
        <div class="modal-content modal-content-large">
            <span class="close" onclick="closeServiceModal()">&times;</span>
            <div class="modal-header">
                <h2 id="modalTitle">Add New Service</h2>
                <h3 id="modalSubtitle">Create a new barangay service</h3>
            </div>

            <form action="<?php echo SITE_URL; ?>/controllers/ServiceController.php" method="POST" id="serviceForm">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="service_id" id="service_id">

                <div class="form-group">
                    <label for="service_name">Service Name *</label>
                    <input type="text" id="service_name" name="service_name" required
                        placeholder="e.g., Barangay Clearance">
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" rows="3" required
                        placeholder="Brief description of the service"></textarea>
                </div>

                <div class="form-group">
                    <label for="requirements">Requirements *</label>
                    <textarea id="requirements" name="requirements" rows="3" required
                        placeholder="List of required documents (e.g., Valid ID, Proof of Residency)"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="processing_time">Processing Time *</label>
                        <input type="text" id="processing_time" name="processing_time" required
                            placeholder="e.g., 1-2 days">
                    </div>

                    <div class="form-group">
                        <label for="fee">Service Fee (₱) *</label>
                        <input type="number" id="fee" name="fee" step="0.01" min="0" required
                            placeholder="0.00">
                    </div>
                </div>

                <div class="form-group">
                    <label for="is_active">Status *</label>
                    <select id="is_active" name="is_active" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Create Service</button>
                    <button type="button" onclick="closeServiceModal()" class="btn" style="background-color: #95a5a6; color: white; margin-left: 10px;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
    <script>
        // Modal functions
        function openServiceModal(mode) {
            document.getElementById('serviceModal').style.display = 'block';
            document.body.style.overflow = 'hidden';

            if (mode === 'create') {
                document.getElementById('modalTitle').textContent = 'Add New Service';
                document.getElementById('modalSubtitle').textContent = 'Create a new barangay service';
                document.getElementById('formAction').value = 'create';
                document.getElementById('submitBtn').textContent = 'Create Service';
                document.getElementById('serviceForm').reset();
            }
        }

        function closeServiceModal() {
            document.getElementById('serviceModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function editService(service) {
            document.getElementById('modalTitle').textContent = 'Edit Service';
            document.getElementById('modalSubtitle').textContent = 'Update service information';
            document.getElementById('formAction').value = 'update';
            document.getElementById('submitBtn').textContent = 'Update Service';

            document.getElementById('service_id').value = service.id;
            document.getElementById('service_name').value = service.service_name;
            document.getElementById('description').value = service.description;
            document.getElementById('requirements').value = service.requirements;
            document.getElementById('processing_time').value = service.processing_time;
            document.getElementById('fee').value = service.fee;
            document.getElementById('is_active').value = service.is_active;

            openServiceModal('edit');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('serviceModal');
            if (event.target == modal) {
                closeServiceModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeServiceModal();
            }
        });
    </script>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 99999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: white;
            margin: 2% auto;
            padding: 0;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-content-large {
            max-width: 700px;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 20px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            z-index: 1;
        }

        .close:hover {
            color: #333;
        }

        .modal-header {
            padding: 30px 40px 20px;
            text-align: center;
            border-bottom: 1px solid #ecf0f1;
        }

        .modal-header h2 {
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .modal-header h3 {
            color: #7f8c8d;
            font-weight: normal;
            font-size: 1rem;
            margin: 0;
        }

        .modal-content form {
            padding: 20px 40px 30px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .form-group select {
            background-color: white;
            cursor: pointer;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
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
        .form-group input:hover,
        .form-group select:hover,
        .form-group textarea:hover {
            border-color: #95a5a6;
        }
    </style>
    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
</body>

</html>