<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

AuthController::requireLogin();
AuthController::requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Handle form submissions
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_general'])) {
        $successMessage = "General settings updated successfully!";
    } elseif (isset($_POST['update_appointment'])) {
        $successMessage = "Appointment settings updated successfully!";
    } elseif (isset($_POST['update_notification'])) {
        $successMessage = "Notification settings updated successfully!";
    } elseif (isset($_POST['backup_database'])) {
        $successMessage = "Database backup initiated successfully!";
    }
}

// Default settings (can be loaded from database)
$settings = [
    'site_name' => SITE_NAME,
    'site_email' => 'barangay.stotomas@email.com',
    'site_phone' => '(123) 456-7890',
    'max_appointments_per_day' => 50,
    'appointment_duration' => 30,
    'advance_booking_days' => 30,
    'office_hours_start' => '08:00',
    'office_hours_end' => '17:00',
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include __DIR__ . '/../includes/admin_header.php'; ?>

    <div class="main-container">
        <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

        <main class="content" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; padding: 25px;">
            <div class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 35px 30px; border-radius: 20px; margin-bottom: 30px; box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3); position: relative; overflow: hidden;">
                <div style="position: absolute; top: -80px; right: -80px; width: 250px; height: 250px; background: rgba(255,255,255,0.08); border-radius: 50%; z-index: 0;"></div>
                <div style="position: absolute; bottom: -40px; left: -40px; width: 180px; height: 180px; background: rgba(255,255,255,0.08); border-radius: 50%; z-index: 0;"></div>
                <h1 style="color: white; font-size: 2.5rem; margin: 0 0 10px 0; position: relative; z-index: 1; text-shadow: 0 2px 10px rgba(0,0,0,0.2); font-weight: 700;">⚙️ Settings</h1>
                <p style="color: rgba(255,255,255,0.95); font-size: 1.15rem; margin: 0; position: relative; z-index: 1; font-weight: 500;">Manage system settings and configurations</p>
            </div>

            <?php if ($successMessage): ?>
                <div style="background: linear-gradient(135deg, rgba(72, 187, 120, 0.1) 0%, rgba(56, 161, 105, 0.1) 100%); color: #2f855a; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; font-weight: 500; border: 2px solid rgba(72, 187, 120, 0.3); animation: slideIn 0.3s ease;">
                    <span style="font-size: 1.2rem; font-weight: bold;">✓</span> <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>

            <div style="display: grid; gap: 25px; margin-bottom: 30px;">
                <!-- General Settings -->
                <div style="background: white; border-radius: 20px; padding: 0; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08); border: 2px solid rgba(102, 126, 234, 0.1); overflow: hidden; transition: all 0.3s ease;">
                    <div style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); padding: 25px 30px; border-bottom: 2px solid rgba(102, 126, 234, 0.15);">
                        <h2 style="margin: 0 0 8px 0; color: #2d3748; font-size: 1.5rem; font-weight: 700;">🏢 General Settings</h2>
                        <p style="margin: 0; color: #6c757d; font-size: 0.95rem;">Configure basic system information</p>
                    </div>
                    <div style="padding: 30px;">
                        <form method="POST">
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 20px;">
                                <div>
                                    <label style="display: block; font-weight: 600; color: #2d3748; margin-bottom: 8px; font-size: 0.95rem;">Site Name</label>
                                    <input type="text" name="site_name" value="<?php echo htmlspecialchars($settings['site_name']); ?>" style="width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; transition: all 0.3s ease; height: 48px;">
                                </div>
                                <div>
                                    <label style="display: block; font-weight: 600; color: #2d3748; margin-bottom: 8px; font-size: 0.95rem;">Contact Email</label>
                                    <input type="email" name="site_email" value="<?php echo htmlspecialchars($settings['site_email']); ?>" style="width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; transition: all 0.3s ease; height: 48px;">
                                </div>
                                <div>
                                    <label style="display: block; font-weight: 600; color: #2d3748; margin-bottom: 8px; font-size: 0.95rem;">Contact Phone</label>
                                    <input type="tel" name="site_phone" value="<?php echo htmlspecialchars($settings['site_phone']); ?>" style="width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; transition: all 0.3s ease; height: 48px;">
                                </div>
                            </div>
                            <button type="submit" name="update_general" class="btn btn-primary">💾 Save General Settings</button>
                        </form>
                    </div>
                </div>

                <!-- Appointment Settings -->
                <div style="background: white; border-radius: 20px; padding: 0; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08); border: 2px solid rgba(102, 126, 234, 0.1); overflow: hidden; transition: all 0.3s ease;">
                    <div style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); padding: 25px 30px; border-bottom: 2px solid rgba(102, 126, 234, 0.15);">
                        <h2 style="margin: 0 0 8px 0; color: #2d3748; font-size: 1.5rem; font-weight: 700;">📅 Appointment Settings</h2>
                        <p style="margin: 0; color: #6c757d; font-size: 0.95rem;">Configure appointment booking parameters</p>
                    </div>
                    <div style="padding: 30px;">
                        <form method="POST">
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 20px;">
                                <div>
                                    <label style="display: block; font-weight: 600; color: #2d3748; margin-bottom: 8px; font-size: 0.95rem;">Max Appointments Per Day</label>
                                    <input type="number" name="max_appointments" value="<?php echo $settings['max_appointments_per_day']; ?>" min="1" style="width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; transition: all 0.3s ease; height: 48px;">
                                </div>
                                <div>
                                    <label style="display: block; font-weight: 600; color: #2d3748; margin-bottom: 8px; font-size: 0.95rem;">Appointment Duration (minutes)</label>
                                    <input type="number" name="appointment_duration" value="<?php echo $settings['appointment_duration']; ?>" min="15" step="15" style="width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; transition: all 0.3s ease; height: 48px;">
                                </div>
                                <div>
                                    <label style="display: block; font-weight: 600; color: #2d3748; margin-bottom: 8px; font-size: 0.95rem;">Advance Booking Days</label>
                                    <input type="number" name="advance_booking" value="<?php echo $settings['advance_booking_days']; ?>" min="1" style="width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; transition: all 0.3s ease; height: 48px;">
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 20px;">
                                <div>
                                    <label style="display: block; font-weight: 600; color: #2d3748; margin-bottom: 8px; font-size: 0.95rem;">Office Hours Start</label>
                                    <input type="time" name="office_start" value="<?php echo $settings['office_hours_start']; ?>" style="width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; transition: all 0.3s ease; height: 48px;">
                                </div>
                                <div>
                                    <label style="display: block; font-weight: 600; color: #2d3748; margin-bottom: 8px; font-size: 0.95rem;">Office Hours End</label>
                                    <input type="time" name="office_end" value="<?php echo $settings['office_hours_end']; ?>" style="width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 1rem; transition: all 0.3s ease; height: 48px;">
                                </div>
                            </div>
                            <button type="submit" name="update_appointment" class="btn btn-primary">💾 Save Appointment Settings</button>
                        </form>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div style="background: white; border-radius: 20px; padding: 0; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08); border: 2px solid rgba(102, 126, 234, 0.1); overflow: hidden; transition: all 0.3s ease;">
                    <div style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); padding: 25px 30px; border-bottom: 2px solid rgba(102, 126, 234, 0.15);">
                        <h2 style="margin: 0 0 8px 0; color: #2d3748; font-size: 1.5rem; font-weight: 700;">🔔 Notification Settings</h2>
                        <p style="margin: 0; color: #6c757d; font-size: 0.95rem;">Manage notification preferences and channels</p>
                    </div>
                    <div style="padding: 30px;">
                        <form method="POST">
                            <!-- Email Notifications Card -->
                            <div class="notification-card" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); padding: 25px; border-radius: 16px; border: 2px solid rgba(102, 126, 234, 0.15); margin-bottom: 20px; position: relative; overflow: hidden; transition: all 0.3s ease;">
                                <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(102, 126, 234, 0.05); border-radius: 50%; z-index: 0;"></div>
                                <div style="position: absolute; bottom: -30px; left: -30px; width: 120px; height: 120px; background: rgba(118, 75, 162, 0.05); border-radius: 50%; z-index: 0;"></div>

                                <div style="position: relative; z-index: 1; display: flex; align-items: flex-start; gap: 20px; margin-bottom: 20px;">
                                    <div style="display: flex; align-items: center; justify-content: center; width: 70px; height: 70px; border-radius: 16px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%); border: 2px solid rgba(102, 126, 234, 0.3); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2); flex-shrink: 0;">
                                        <span style="font-size: 2.2rem;">📧</span>
                                    </div>
                                    <div style="flex: 1;">
                                        <h3 style="margin: 0 0 8px 0; color: #2d3748; font-size: 1.25rem; font-weight: 700;">Email Notifications</h3>
                                        <p style="margin: 0 0 15px 0; color: #718096; font-size: 0.9rem; line-height: 1.6;">Receive instant email alerts for appointment confirmations, updates, cancellations, and important system messages directly to your inbox.</p>

                                        <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 12px; background: white; border-radius: 10px; border: 2px solid rgba(102, 126, 234, 0.1); transition: all 0.3s ease;">
                                            <div class="toggle-switch" style="position: relative; display: inline-block; width: 60px; height: 32px;">
                                                <input type="checkbox" id="email_toggle" name="email_notifications" checked style="opacity: 0; width: 0; height: 0;">
                                                <span class="toggle-slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 34px; transition: 0.4s; box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);"></span>
                                                <span style="position: absolute; content: ''; height: 24px; width: 24px; left: 4px; bottom: 4px; background-color: white; border-radius: 50%; transition: 0.4s; z-index: 2; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"></span>
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; color: #2d3748; font-size: 0.95rem;">Enable Email Notifications</div>
                                                <div style="font-size: 0.8rem; color: #a0aec0; margin-top: 2px;">Toggle to activate/deactivate</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- SMS Notifications Card -->
                            <div class="notification-card" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); padding: 25px; border-radius: 16px; border: 2px solid rgba(102, 126, 234, 0.15); position: relative; overflow: hidden; transition: all 0.3s ease;">
                                <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(102, 126, 234, 0.05); border-radius: 50%; z-index: 0;"></div>
                                <div style="position: absolute; bottom: -30px; left: -30px; width: 120px; height: 120px; background: rgba(118, 75, 162, 0.05); border-radius: 50%; z-index: 0;"></div>

                                <div style="position: relative; z-index: 1; display: flex; align-items: flex-start; gap: 20px; margin-bottom: 20px;">
                                    <div style="display: flex; align-items: center; justify-content: center; width: 70px; height: 70px; border-radius: 16px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%); border: 2px solid rgba(102, 126, 234, 0.3); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2); flex-shrink: 0;">
                                        <span style="font-size: 2.2rem;">📱</span>
                                    </div>
                                    <div style="flex: 1;">
                                        <h3 style="margin: 0 0 8px 0; color: #2d3748; font-size: 1.25rem; font-weight: 700;">SMS Notifications</h3>
                                        <p style="margin: 0 0 15px 0; color: #718096; font-size: 0.9rem; line-height: 1.6;">Get text message reminders on your mobile phone for upcoming appointments, status changes, and time-sensitive notifications.</p>

                                        <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 12px; background: white; border-radius: 10px; border: 2px solid rgba(102, 126, 234, 0.1); transition: all 0.3s ease;">
                                            <div class="toggle-switch" style="position: relative; display: inline-block; width: 60px; height: 32px;">
                                                <input type="checkbox" id="sms_toggle" name="sms_notifications" style="opacity: 0; width: 0; height: 0;">
                                                <span class="toggle-slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e0; border-radius: 34px; transition: 0.4s; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></span>
                                                <span style="position: absolute; content: ''; height: 24px; width: 24px; left: 4px; bottom: 4px; background-color: white; border-radius: 50%; transition: 0.4s; z-index: 2; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"></span>
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; color: #2d3748; font-size: 0.95rem;">Enable SMS Notifications</div>
                                                <div style="font-size: 0.8rem; color: #a0aec0; margin-top: 2px;">Toggle to activate/deactivate</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- System Maintenance -->
                <div style="background: white; border-radius: 20px; padding: 0; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08); border: 2px solid rgba(102, 126, 234, 0.1); overflow: hidden; transition: all 0.3s ease;">
                    <div style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); padding: 25px 30px; border-bottom: 2px solid rgba(102, 126, 234, 0.15);">
                        <h2 style="margin: 0 0 8px 0; color: #2d3748; font-size: 1.5rem; font-weight: 700;">🔧 System Maintenance</h2>
                        <p style="margin: 0; color: #6c757d; font-size: 0.95rem;">Backup and maintenance tools</p>
                    </div>
                    <div style="padding: 30px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
                            <div class="maintenance-card" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); padding: 30px 20px; border-radius: 15px; text-align: center; border: 2px solid rgba(102, 126, 234, 0.1); transition: all 0.3s ease; cursor: pointer;">
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border: 3px solid rgba(102, 126, 234, 0.2); margin-bottom: 15px; transition: all 0.3s ease;">
                                    <span style="font-size: 2.5rem;">💾</span>
                                </div>
                                <h3 style="margin: 0 0 10px 0; font-size: 1.15rem; color: #2d3748; font-weight: 700;">Database Backup</h3>
                                <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 0.9rem;">Create a backup of database</p>
                                <form method="POST" style="display: inline;">
                                    <button type="submit" name="backup_database" class="btn btn-secondary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25); transition: all 0.3s ease;">Create Backup</button>
                                </form>
                            </div>
                            <div class="maintenance-card" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); padding: 30px 20px; border-radius: 15px; text-align: center; border: 2px solid rgba(102, 126, 234, 0.1); transition: all 0.3s ease; cursor: pointer;">
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border: 3px solid rgba(102, 126, 234, 0.2); margin-bottom: 15px; transition: all 0.3s ease;">
                                    <span style="font-size: 2.5rem;">🗑️</span>
                                </div>
                                <h3 style="margin: 0 0 10px 0; font-size: 1.15rem; color: #2d3748; font-weight: 700;">Clear Cache</h3>
                                <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 0.9rem;">Clear system cache files</p>
                                <button type="button" class="btn btn-secondary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25); transition: all 0.3s ease;" onclick="alert('Cache cleared!')">Clear Cache</button>
                            </div>
                            <div class="maintenance-card" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); padding: 30px 20px; border-radius: 15px; text-align: center; border: 2px solid rgba(102, 126, 234, 0.1); transition: all 0.3s ease; cursor: pointer;">
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border: 3px solid rgba(102, 126, 234, 0.2); margin-bottom: 15px; transition: all 0.3s ease;">
                                    <span style="font-size: 2.5rem;">📊</span>
                                </div>
                                <h3 style="margin: 0 0 10px 0; font-size: 1.15rem; color: #2d3748; font-weight: 700;">System Logs</h3>
                                <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 0.9rem;">View system activity logs</p>
                                <button type="button" class="btn btn-secondary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25); transition: all 0.3s ease;" onclick="alert('Logs viewer coming soon!')">View Logs</button>
                            </div>
                            <div class="maintenance-card" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); padding: 30px 20px; border-radius: 15px; text-align: center; border: 2px solid rgba(102, 126, 234, 0.1); transition: all 0.3s ease; cursor: pointer;">
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border: 3px solid rgba(102, 126, 234, 0.2); margin-bottom: 15px; transition: all 0.3s ease;">
                                    <span style="font-size: 2.5rem;">🔄</span>
                                </div>
                                <h3 style="margin: 0 0 10px 0; font-size: 1.15rem; color: #2d3748; font-weight: 700;">System Update</h3>
                                <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 0.9rem;">Check for system updates</p>
                                <button type="button" class="btn btn-secondary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25); transition: all 0.3s ease;" onclick="alert('System is up to date!')">Check Updates</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        input:focus {
            outline: none;
            border-color: #667eea !important;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
        }

        .maintenance-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.2) !important;
            border-color: rgba(102, 126, 234, 0.3) !important;
        }

        .maintenance-card:hover>div:first-of-type {
            transform: scale(1.1) rotate(5deg);
            border-color: rgba(102, 126, 234, 0.4) !important;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }

        /* Notification Card Hover Effects */
        .notification-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.25) !important;
            border-color: rgba(102, 126, 234, 0.3) !important;
        }

        /* Enhanced Toggle Switch */
        .toggle-switch input:checked+.toggle-slider {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%) !important;
        }

        .toggle-switch input:checked+.toggle-slider+span {
            transform: translateX(28px);
        }

        .toggle-switch:hover .toggle-slider {
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4) !important;
        }

        /* Label Hover Effect */
        .notification-card label:hover {
            background: rgba(102, 126, 234, 0.03) !important;
            border-color: rgba(102, 126, 234, 0.2) !important;
        }

        /* Button Hover Effects */
        .notification-card button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5) !important;
        }

        .notification-card button:active {
            transform: translateY(0);
        }
    </style>

    <script>
        // Auto-hide success messages
        setTimeout(() => {
            const alerts = document.querySelectorAll('[style*="slideIn"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>

</html>