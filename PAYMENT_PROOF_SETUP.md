# Payment Proof Feature - Setup Instructions

## Overview

The payment proof feature allows users to upload images of their payment receipts for approved appointments. Admins can then view, verify, and manage these payment proofs.

## Setup Steps

### 1. Run Database Migration

Execute the SQL migration to add payment proof columns to the appointments table:

**Option A: Using phpMyAdmin**

1. Open phpMyAdmin in your browser (usually http://localhost/phpmyadmin)
2. Select the `barangay_appointment` database
3. Click on "SQL" tab
4. Copy and paste the contents of `database/add_payment_proof.sql`
5. Click "Go" to execute

**Option B: Using MySQL Command Line**

```bash
mysql -u root -p barangay_appointment < database/add_payment_proof.sql
```

### 2. Verify Directory Permissions

Ensure the uploads directory is writable:

```bash
# Windows (PowerShell)
icacls "uploads\payment_proofs" /grant Users:F

# The upload handler will automatically create the directory if it doesn't exist
```

### 3. Test the Feature

**As a User:**

1. Login with a user account
2. Go to Dashboard
3. Find an approved appointment with a fee > 0
4. Click the "📤 Upload Proof" button
5. Select an image file (JPG, PNG, or PDF, max 5MB)
6. Preview will show (for images)
7. Add optional notes
8. Click "Upload Payment Proof"
9. You should see a success message
10. The button changes to "✓ Proof Uploaded"

**As an Admin:**

1. Login with admin account
2. Go to Appointments
3. Click "View" on any appointment with uploaded payment proof
4. Scroll to "💳 Payment Proof" section
5. You can:
   - View the uploaded image (click to enlarge)
   - Download the proof
   - View upload timestamp
   - See any notes added by the user

## Features Implemented

### User Side:

- Upload payment proof button (only for approved appointments with fees)
- Image preview before upload
- File validation (type and size)
- Upload status indicator
- View uploaded proof in appointment details
- Download own payment proof

### Admin Side:

- View payment proof in appointment details
- Click to enlarge image
- Download payment proof
- View upload timestamp
- No payment indicator if not uploaded

## File Upload Specifications

- **Accepted Formats:** JPG, JPEG, PNG, PDF
- **Maximum Size:** 5MB
- **Storage Location:** `uploads/payment_proofs/`
- **File Naming:** `payment_{appointment_id}_{timestamp}_{unique_id}.{extension}`

## Security Features

- User authentication required
- Users can only upload for their own appointments
- Only approved appointments can receive payment proof
- File type validation (MIME type check)
- File size validation
- Automatic old file deletion when uploading new proof
- Secure file naming (prevents overwrites)

## Database Changes

The migration adds two columns to the `appointments` table:

- `payment_proof` VARCHAR(255) - Stores the relative file path
- `payment_proof_uploaded_at` TIMESTAMP - Records upload date/time

## Files Created/Modified

### New Files:

1. `database/add_payment_proof.sql` - Database migration
2. `controllers/upload_payment_proof.php` - Upload handler
3. `uploads/payment_proofs/README.md` - Directory documentation

### Modified Files:

1. `views/admin/view_appointment.php` - Added payment proof display section
2. `views/user/dashboard.php` - Added upload button and modal
3. `views/user/view_appointment.php` - Added payment proof display section

## Troubleshooting

### Upload fails with "Permission denied"

- Ensure the `uploads/payment_proofs` directory is writable
- Check that PHP has permission to create directories

### "File too large" error

- Check PHP settings: `upload_max_filesize` and `post_max_size`
- Both should be at least 5MB

### Image doesn't display

- Check that the file was actually uploaded to `uploads/payment_proofs/`
- Verify the path in the database is correct
- Ensure web server can serve files from uploads directory

### Database error

- Make sure you ran the SQL migration script
- Verify the columns `payment_proof` and `payment_proof_uploaded_at` exist in `appointments` table

## Support

For issues or questions, check:

1. PHP error logs
2. Browser console for JavaScript errors
3. File permissions in uploads directory
4. Database column existence
