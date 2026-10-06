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

## 7. Step 6: Deploy Database to Render (Prevent Data Removal on Sleep)

### Why Data is Removed After 15–30 Minutes on Render Free Tier:
Render Free Web Services **spin down (sleep) after 15 minutes of inactivity**, and their local container filesystems are **ephemeral**. When Render spins back up, a new container instance starts with an empty filesystem, causing embedded local databases to reset back to the default seed!

### The Permanent Fix (Free Persistent Cloud MySQL):
Connect your Render Web Service to a free persistent cloud MySQL database such as **TiDB Cloud Serverless** (Free forever, 5GB, no credit card, 100% MySQL 8.0) or **Aiven for MySQL**.

Our container is now configured to **automatically initialize an empty cloud database on first connect** (`spark db:init-production`) and **never overwrite data** on subsequent restarts or spin-downs!

---

### Recommended: TiDB Cloud Serverless (100% Free Forever, 2 Minutes Setup)

1. **Sign up at [tidbcloud.com](https://tidbcloud.com)** (Free sign-up with GitHub or Google, no credit card needed).
2. **Create a Serverless Cluster**:
   - Select **Serverless (Free)**.
   - Choose a region close to your Render service (e.g. AWS `us-east-1` or `eu-central-1`).
   - Click **Create**.
3. **Get Connection Parameters**:
   - Click **Connect** ➔ Select **General** connection format.
   - You will see:
     - **Host**: `gateway01.us-east-1.prod.aws.tidbcloud.com` (or similar)
     - **Port**: `4000`
     - **User**: `xxxxxxxx.root`
     - **Password**: `<your-generated-password>`
     - **Database**: `test` (or create a database named `real_estate_erp`)

4. **Add Environment Variables in Render Dashboard**:
   - Go to [dashboard.render.com](https://dashboard.render.com) ➔ Select your Web Service (`real-estate-erp-dxbd`).
   - Click **Environment** ➔ Add / Update:

| Key | Example Value | Description |
| :--- | :--- | :--- |
| `CI_ENVIRONMENT` | `production` | Production mode |
| `APP_BASE_URL` | `https://real-estate-erp-dxbd.onrender.com/` | Public service URL |
| `DB_HOST` | `gateway01.us-east-1.prod.aws.tidbcloud.com` | TiDB host |
| `DB_PORT` | `4000` | TiDB port |
| `DB_DATABASE` | `test` (or `real_estate_erp`) | Database name |
| `DB_USERNAME` | `xxxxxxxx.root` | TiDB username |
| `DB_PASSWORD` | `<your_password>` | TiDB password |
| `DB_SSL` | `true` | Enables TLS encryption (auto-detected) |

*(Alternatively, you can provide a single `DATABASE_URL` string if preferred).*

5. **Deploy / Save Changes**:
   - Render will immediately restart the web service container.
   - On startup, the container connects to TiDB, detects that it is fresh (0 tables), and imports all 75 tables automatically from `real_estate_erp.sql`.
   - From this point on, **any user, property, or change you save is permanently stored in TiDB Cloud and will NEVER be deleted when Render sleeps or restarts!**
