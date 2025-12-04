# Payment Proofs Upload Directory

This directory stores uploaded payment proof images from users.

## Important Notes:

- This folder is automatically created by the upload handler if it doesn't exist
- Uploaded files are named with format: `payment_{appointment_id}_{timestamp}_{unique_id}.{extension}`
- Supported file types: JPG, JPEG, PNG, PDF
- Maximum file size: 5MB
- Files are secured and only accessible through the application

## Security:

- Access to this directory should be restricted
- Configure web server to prevent direct listing of files
- Only authenticated users can upload files for their own appointments
- Admin users can view all payment proofs through the appointment view page
