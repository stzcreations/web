 HEAD
# STZ Creations - Project Download Library

Pure PHP 8.2+ + MySQL project download library website.

## Requirements
- PHP 8.2+
- MySQL 5.7+ / MariaDB
- Apache with mod_rewrite (XAMPP recommended)

## Localhost (XAMPP) Setup
1. Copy project folder to `C:\xampp\htdocs\stzcreations`.
2. Start Apache and MySQL in XAMPP Control Panel.
3. Import database: open phpMyAdmin or run:
   `mysql -u root < database/database.sql`
4. Ensure `config/config.php` exists (copy from `config/config.example.php` and edit).
5. Create folders if missing: `uploads/thumbnails`, `uploads/screenshots`, `uploads/projects`, `uploads/ads`, `storage/projects`.
6. Visit `http://localhost/stzcreations/`

## Default Admin
- URL: `http://localhost/stzcreations/admin`
- Username: `admin`
- Password: `admin123`

## Installation Wizard
Visit `http://localhost/stzcreations/install.php` (delete `storage/installed.lock` to re-run).

## Folder Structure
See spec. Main entry points: index.php, browse.php, project.php, download.php, admin/index.php.

## Security Notes
- Project files stored in `storage/projects` (denied direct access).
- Downloads served via `download.php` with validation.
- CSRF protection on all forms.
=======
# stz-creations
A premium dynamic portfolio website for STZ Creations, built with PHP and MySQL. Features a secure admin dashboard, project management, social media integration, and customizable website settings.
>>>>>>> 350f63def0527ac9c80f0ff2b4d440283bc32805
