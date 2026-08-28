# 🚀 Production Deployment Guide

This guide provides step-by-step instructions to deploy the portfolio to any standard PHP/MySQL hosting provider (e.g., **AlwaysData**, **cPanel**, **Hostinger**, **OVH**, or **VPS / Apache**).

---

## 📋 Requirements

- **PHP**: `8.1` or higher (with extensions: `pdo_mysql`, `fileinfo`, `mbstring`, `session`)
- **Web Server**: Apache (with `mod_rewrite` & `mod_headers` enabled) or Nginx
- **Database**: MySQL 5.7+ / MariaDB 10.3+

---

## 📁 1. Project Structure & Files to Upload

Upload the entire portfolio project to your web root (`public_html/`, `www/`, or your site's document root on AlwaysData).

Ensure the following core directories and files are present:
```text
├── .htaccess               <- Security headers, deny direct access to config/env
├── index.php               <- Root router & entry point
├── setup.php               <- One-time visual setup wizard
├── index.html              <- Homepage
├── about.html              <- About page
├── contact.html            <- Contact page
├── experience.html         <- Experience page
├── skills.html             <- Skills page
├── admin/                  <- Admin panel (dashboard, login, project & tech forms)
├── api/                    <- REST JSON endpoints (projects, technologies)
├── config/                 <- App & database configuration (app.php, db.php)
├── database/               <- Database schema (schema.sql, setup.php)
├── includes/               <- Common HTML headers/footers & Validator.php
├── css/                    <- Design tokens, base, components, and admin styles
├── js/                     <- Dynamic data fetch, theme switcher, i18n, main scripts
└── uploads/                <- User uploaded images (projects, tech icons)
    ├── .htaccess           <- Prevents execution of scripts inside uploads
    ├── projects/
    └── tech/
```

---

## 🗄️ 2. Create MySQL Database & Import Schema

### Option A: Via phpMyAdmin (Recommended for AlwaysData / cPanel)
1. Log into your hosting dashboard and open **phpMyAdmin**.
2. Create a new database (e.g. `youruser_portfolio` or `portfolio_db`) with collation `utf8mb4_unicode_ci`.
3. Click on the **Import** tab.
4. Select the file [`database/schema.sql`](file:///c:/xampp2/htdocs/Portfolio/database/schema.sql) from your local project and click **Import**.
5. Note your database credentials:
   - **Database Host** (e.g. `mysql-yourname.alwaysdata.net` or `localhost` / `127.0.0.1`)
   - **Database Name**
   - **Database User**
   - **Database Password**

### Option B: Via SSH / Terminal
```bash
mysql -h <DB_HOST> -u <DB_USER> -p <DB_NAME> < database/schema.sql
```

---

## ⚙️ 3. Environment Configuration (`.env`)

You can configure the project in one of two ways:

### Method 1: Automatic Setup Wizard (Easiest)
1. Open your domain in your browser: `https://your-portfolio-domain.com/`
2. You will be automatically redirected to `/setup.php`.
3. Enter your desired **Admin Username** and a secure **Password** (min. 8 characters).
4. Submit the form. The wizard will automatically create `.env` and `.setup-complete`.
5. Open `.env` (via File Manager or FTP) to ensure your database connection credentials match your hosting database:

```env
DB_HOST=mysql-yourname.alwaysdata.net
DB_NAME=youruser_portfolio
DB_USER=youruser_db
DB_PASS=your_strong_password
APP_TIMEZONE=Africa/Conakry
ADMIN_USERNAME=admin
ADMIN_PASSWORD_HASH=$2y$10$...
```

### Method 2: Manual `.env` File Creation
Create a file named `.env` at the root of your project:
```env
DB_HOST=127.0.0.1
DB_NAME=portfolio_db
DB_USER=your_db_user
DB_PASS=your_db_password
APP_TIMEZONE=Africa/Conakry
ADMIN_USERNAME=admin
ADMIN_PASSWORD_HASH=$2y$10$YOUR_GENERATED_BCRYPT_HASH
```

> **Tip**: To generate a bcrypt hash locally:
> ```bash
> php -r "echo password_hash('YourSecurePassword', PASSWORD_BCRYPT), PHP_EOL;"
> ```
> Also create an empty marker file named `.setup-complete` at the project root to mark the setup as finished.

---

## 🔒 4. File Permissions & Security

Ensure write permissions for the PHP process on upload folders and configuration files:

```bash
# Upload folders (read, write, execute)
chmod -R 755 uploads/

# Configuration files (read & write for owner only)
chmod 600 .env
chmod 644 .htaccess
```

### Security Built-in:
- `.htaccess` blocks public web access to `.env`, `config/`, and `database/`.
- `uploads/.htaccess` disables PHP script execution inside the upload directories to prevent webshell uploads.
- Form submissions are guarded with CSRF protection and schema-based input sanitization.

---

## 🔑 5. Accessing the Admin Panel

1. Go to: `https://your-portfolio-domain.com/admin/login.php`
2. Log in using your configured **Username** and **Password**.
3. From the dashboard:
   - Add, edit, or delete **Projects**.
   - Manage your **Technologies** list.
   - Upload screenshots and project logos.

---

## ✅ 6. Post-Deployment Verification Checklist

- [ ] **Homepage (`/`)**: Displays hero section, dynamic projects, and technology carousel.
- [ ] **Projects Page (`/projects/index.php`)**: Category filters work (Web, Mobile, Gestion, Autres) and load cards from MySQL.
- [ ] **API Endpoints**:
  - `GET /api/projects.php?limit=6` returns valid JSON with status 200.
  - `GET /api/technologies.php` returns active technologies JSON.
- [ ] **Admin Login (`/admin/login.php`)**: Authenticates successfully and redirects to `/admin/dashboard.php`.
- [ ] **Image Uploads**: Creating a project with an image correctly saves the file in `uploads/projects/` and displays it.
- [ ] **Theme & Language**: Dark/light mode switcher and French/English toggle work smoothly.

---

## ❓ 7. Troubleshooting & FAQ

| Issue | Cause | Solution |
| :--- | :--- | :--- |
| **Redirects in loop to `/setup.php`** | Missing `.env` or `.setup-complete` | Visit `/setup.php` and submit the first-launch setup form, or create `.setup-complete` manually. |
| **500 Internal Server Error** | Missing PHP extension or DB credentials error | Check server error logs. Verify `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS` in `.env`. |
| **Uploaded images return 403 or fail to upload** | Directory permissions or missing folder | Ensure `uploads/projects` and `uploads/tech` exist and have `755` permissions. |
| **Database error on `/projects/index.php`** | Database schema not imported | Import [`database/schema.sql`](file:///c:/xampp2/htdocs/Portfolio/database/schema.sql) in phpMyAdmin. |
| **CSS/JS not loading** | Incorrect base URL or rewrite issues | Ensure `.htaccess` is uploaded and Apache `mod_headers` & `mod_rewrite` are active. |
