# Real Estate ERP — Database Deployment & Backup Guide

This directory contains the production-ready database dump and import scripts for the **Real Estate ERP / CRM** application.

---

## 1. Overview

- **Database Name**: `real_estate_erp`
- **Engine**: MySQL 8.0+ / MariaDB 10.5+
- **Default Character Set**: `utf8mb4`
- **Default Collation**: `utf8mb4_unicode_ci`
- **Total Tables**: 75 tables
- **Foreign Key Constraints**: 129 relational constraints
- **Pre-populated Records**: ~3,824 rows across all 44 ERP modules (including Super Admin, Operations Admin, roles, 232 granular permissions, system settings, sample projects, branches, and properties).
- **SQL Dump File**: `database/backups/real_estate_erp.sql` (~644 KB)

---

## 2. Step 1: Create an Empty MySQL Database

Connect to your MySQL server (via MySQL CLI, phpMyAdmin, DBeaver, or Antigravity Database Client):

```sql
CREATE DATABASE IF NOT EXISTS `real_estate_erp`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

---

## 3. Step 2: Import the SQL Dump

### Option A: Using the Automated Import Script

#### On Linux / macOS / Render Shell:
```bash
chmod +x database/import_database.sh

# Run interactively (prompts for password securely):
./database/import_database.sh

# Or pass custom host, port, database, user:
./database/import_database.sh <db_host> 3306 real_estate_erp <db_user>
```

#### On Windows:
```cmd
database\import_database.bat

# Or with arguments:
database\import_database.bat 127.0.0.1 3306 real_estate_erp root
```

### Option B: Using Standard MySQL CLI Command

```bash
# Local MySQL:
mysql -h 127.0.0.1 -P 3306 -u root -p real_estate_erp < database/backups/real_estate_erp.sql

# Remote / Cloud MySQL (e.g., Render / Aiven):
mysql -h <REMOTE_HOST> -P <REMOTE_PORT> -u <REMOTE_USER> -p <REMOTE_DATABASE> < database/backups/real_estate_erp.sql
```

---

## 4. Step 3: Verify the Tables

After import, verify that all 75 tables are present:

```sql
USE `real_estate_erp`;
SHOW TABLES;
SELECT COUNT(*) AS total_tables FROM information_schema.tables WHERE table_schema = 'real_estate_erp';
```

Check the Super Admin account and migrations tracking table:

```sql
SELECT id, name, email, status FROM users LIMIT 3;
SELECT version, class FROM migrations ORDER BY id DESC LIMIT 5;
```

Expected Super Admin: `admin@realestate-erp.local` (Active).

---

## 5. Step 4: Configure CodeIgniter

CodeIgniter dynamically resolves its database configuration via environment variables.

### Local Development (`.env` file)
Ensure your `.env` file contains:

```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'

database.default.hostname = 127.0.0.1
database.default.port = 3306
database.default.database = real_estate_erp
database.default.username = root
database.default.password = your_local_password
database.default.DBDriver = MySQLi
database.default.charset = utf8mb4
database.default.DBCollat = utf8mb4_unicode_ci
```

---

## 6. Step 5: Run the Real Estate ERP Locally

Start the CodeIgniter development server:

```bash
php spark serve --port 8080
```

Open your browser at `http://localhost:8080/login`:
- **Default Super Admin**: `admin@realestate-erp.local`
- **Default Password**: `Admin@123` (or your configured local password)

---

## 7. Step 6: Deploy Database to Render

Render hosts applications as Docker Web Services. Because Render does not offer managed MySQL directly (it offers native PostgreSQL), your MySQL database can be hosted on a cloud MySQL provider such as **Aiven MySQL**, **Render Private MySQL Service**, **Supabase MySQL**, or **Railway MySQL**.

### Step-by-Step Render Cloud Database Setup:

1. **Provision MySQL Instance**:
   - Create a MySQL 8.x database on your preferred cloud provider (e.g. Aiven free tier, Render Docker MySQL private service).
   - Note the connection credentials:
     - `DB_HOST` (e.g. `mysql-xxxx.aivencloud.com`)
     - `DB_PORT` (e.g. `12345` or `3306`)
     - `DB_DATABASE` (e.g. `defaultdb` or `real_estate_erp`)
     - `DB_USERNAME` (e.g. `avnadmin`)
     - `DB_PASSWORD` (e.g. your cloud password)

2. **Import the SQL Dump into Cloud MySQL**:
   From your local computer or terminal, import the dump directly:
   ```bash
   mysql -h <DB_HOST> -P <DB_PORT> -u <DB_USERNAME> -p <DB_DATABASE> < database/backups/real_estate_erp.sql
   ```
   *(Note: If your cloud database requires SSL, add `--ssl-mode=REQUIRED` or `--ssl-ca` as required by your provider).*

3. **Configure Environment Variables in Render Dashboard**:
   - Go to [dashboard.render.com](https://dashboard.render.com) ➔ Select your Web Service (`real-estate-erp-dxdb`).
   - Click **Environment** ➔ Add / Update:
     | Key | Value | Description |
     | :--- | :--- | :--- |
     | `CI_ENVIRONMENT` | `production` | Production mode |
     | `APP_BASE_URL` | `https://real-estate-erp-dxdb.onrender.com/` | Public service URL |
     | `DB_HOST` | `<remote_host>` | Remote database hostname |
     | `DB_PORT` | `3306` (or custom) | Database port |
     | `DB_DATABASE` | `<remote_database>` | Database name |
     | `DB_USERNAME` | `<remote_user>` | Database user |
     | `DB_PASSWORD` | `<remote_password>` | Database password |

4. **Deploy Web Service**:
   - Click **Manual Deploy** ➔ **Deploy latest commit**.
   - Render will launch the container, connect to the remote database, and serve the application live!
