# SQLite to MySQL Migration Guide

## Overview
This guide walks you through migrating your Clinic Management System from SQLite to AWS RDS MySQL.

## Step 1: Export Data from SQLite

### Option A: Using PHP Script (Recommended)
```php
<?php
// export_sqlite_to_mysql.php

// Connect to SQLite
$sqlite_db = new PDO('sqlite:clinic_management.sqlite');

// Get all tables
$tables = $sqlite_db->query("SELECT name FROM sqlite_master WHERE type='table';")->fetchAll(PDO::FETCH_ASSOC);

// Array to store exported data
$data = [];

foreach ($tables as $table) {
    $table_name = $table['name'];
    echo "Exporting table: $table_name\n";
    
    $rows = $sqlite_db->query("SELECT * FROM $table_name")->fetchAll(PDO::FETCH_ASSOC);
    $data[$table_name] = $rows;
}

// Export to JSON
file_put_contents('clinic_data.json', json_encode($data, JSON_PRETTY_PRINT));
echo "✅ Data exported to clinic_data.json\n";
?>
```

Run it:
```bash
php export_sqlite_to_mysql.php
```

### Option B: Using Command Line
```bash
# Export SQLite to SQL file
sqlite3 clinic_management.sqlite ".dump" > sqlite_dump.sql
```

## Step 2: Set Up AWS RDS MySQL

### A. Create RDS Instance
```bash
aws rds create-db-instance \
  --db-instance-identifier clinic-management-db \
  --db-instance-class db.t3.micro \
  --engine mysql \
  --engine-version 8.0.35 \
  --master-username admin \
  --master-user-password "YourSecurePassword123!" \
  --allocated-storage 20 \
  --storage-type gp2 \
  --vpc-security-group-ids sg-xxxxx \
  --db-subnet-group-name clinic-db-subnet \
  --region us-east-1 \
  --multi-az false \
  --publicly-accessible false
```

### B. Wait for Instance to be Available
```bash
aws rds describe-db-instances \
  --db-instance-identifier clinic-management-db \
  --query 'DBInstances[0].DBInstanceStatus'
```

Get the endpoint:
```bash
aws rds describe-db-instances \
  --db-instance-identifier clinic-management-db \
  --query 'DBInstances[0].Endpoint.Address'
```

## Step 3: Import Schema to MySQL

### Create the Database
```bash
# From your local machine with MySQL client installed
MYSQL_HOST="clinic-management-db.xxxxx.us-east-1.rds.amazonaws.com"
MYSQL_USER="admin"
MYSQL_PASSWORD="YourSecurePassword123!"

# Create database
mysql -h $MYSQL_HOST -u $MYSQL_USER -p$MYSQL_PASSWORD -e "CREATE DATABASE clinic_management;"
```

### Import SQL Schema (Already MySQL Compatible!)
```bash
# Your clinic_management.sql is already MySQL compatible
mysql -h $MYSQL_HOST -u $MYSQL_USER -p$MYSQL_PASSWORD clinic_management < clinic_management.sql
```

✅ **Good News:** Your `clinic_management.sql` is already in MySQL format!

## Step 4: Migrate Data from SQLite

### Using PHP Migration Script
```php
<?php
// migrate_data.php

// SQLite connection
$sqlite = new PDO('sqlite:clinic_management.sqlite');

// MySQL connection
$mysql = new PDO(
    'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE'),
    getenv('DB_USERNAME'),
    getenv('DB_PASSWORD'),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Tables to migrate (in order to respect foreign keys)
$tables = ['users', 'doctors', 'patients', 'appointments', 'treatments', 'payments'];

foreach ($tables as $table) {
    echo "Migrating table: $table\n";
    
    // Get all rows from SQLite
    $stmt = $sqlite->query("SELECT * FROM $table");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($rows)) {
        echo "  ⚠️ No data in $table\n";
        continue;
    }
    
    // Get column names
    $columns = array_keys($rows[0]);
    $col_string = implode(', ', $columns);
    $val_placeholders = implode(', ', array_fill(0, count($columns), '?'));
    
    // Prepare insert statement
    $insert_sql = "INSERT INTO $table ($col_string) VALUES ($val_placeholders)";
    $insert_stmt = $mysql->prepare($insert_sql);
    
    // Insert each row
    foreach ($rows as $row) {
        try {
            $insert_stmt->execute(array_values($row));
        } catch (Exception $e) {
            echo "  ⚠️ Error inserting row: " . $e->getMessage() . "\n";
        }
    }
    
    echo "  ✅ Migrated " . count($rows) . " rows\n";
}

echo "✅ Migration complete!\n";
?>
```

Run it:
```bash
# Set environment variables
export DB_HOST="clinic-management-db.xxxxx.us-east-1.rds.amazonaws.com"
export DB_DATABASE="clinic_management"
export DB_USERNAME="admin"
export DB_PASSWORD="YourSecurePassword123!"

php migrate_data.php
```

### Using MySQL Command Line (If data is simple)
```bash
# Disable foreign key checks temporarily
mysql -h $MYSQL_HOST -u $MYSQL_USER -p$MYSQL_PASSWORD clinic_management -e "SET FOREIGN_KEY_CHECKS=0;"

# Insert data (after exporting from SQLite)
# Note: You'll need to manually extract data from SQLite and format it

# Re-enable foreign key checks
mysql -h $MYSQL_HOST -u $MYSQL_USER -p$MYSQL_PASSWORD clinic_management -e "SET FOREIGN_KEY_CHECKS=1;"
```

## Step 5: Update Application Configuration

### Update Your PHP Connection Code

**Before (SQLite):**
```php
<?php
$db = new PDO('sqlite:clinic_management.sqlite');
?>
```

**After (MySQL):**
```php
<?php
$host = getenv('DB_HOST');
$db_name = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

$db = new PDO(
    "mysql:host=$host;dbname=$db_name;charset=utf8mb4",
    $user,
    $password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]
);
?>
```

### Update `.env` File
```bash
# Copy to .env
cp .env.example .env

# Edit with your values
DB_CONNECTION=mysql
DB_HOST=clinic-management-db.xxxxx.us-east-1.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=clinic_management
DB_USERNAME=admin
DB_PASSWORD=YourSecurePassword123!
```

## Step 6: Verify Migration

### Check Row Counts
```bash
# SQLite
sqlite3 clinic_management.sqlite "SELECT COUNT(*) as count FROM users;"

# MySQL
mysql -h $MYSQL_HOST -u $MYSQL_USER -p$MYSQL_PASSWORD -e "SELECT COUNT(*) as count FROM clinic_management.users;"
```

### Compare All Tables
```php
<?php
// verify_migration.php

$sqlite = new PDO('sqlite:clinic_management.sqlite');
$mysql = new PDO(
    'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE'),
    getenv('DB_USERNAME'),
    getenv('DB_PASSWORD')
);

$tables = ['users', 'doctors', 'patients', 'appointments', 'treatments', 'payments'];

echo "Table Comparison:\n";
echo str_repeat("-", 50) . "\n";

foreach ($tables as $table) {
    $sqlite_count = $sqlite->query("SELECT COUNT(*) FROM $table")->fetchColumn();
    $mysql_count = $mysql->query("SELECT COUNT(*) FROM $table")->fetchColumn();
    
    $status = ($sqlite_count == $mysql_count) ? "✅" : "❌";
    echo "$status $table: SQLite=$sqlite_count, MySQL=$mysql_count\n";
}
?>
```

## Step 7: Database Backup Strategy

### Automated RDS Backups
```bash
# Enable automated backups (already default)
aws rds modify-db-instance \
  --db-instance-identifier clinic-management-db \
  --backup-retention-period 7 \
  --preferred-backup-window "03:00-04:00" \
  --apply-immediately
```

### Manual Backup
```bash
# Create snapshot
aws rds create-db-snapshot \
  --db-instance-identifier clinic-management-db \
  --db-snapshot-identifier clinic-backup-2026-07-17
```

### Export to S3
```bash
aws rds start-export-task \
  --export-task-identifier clinic-export-2026-07-17 \
  --source-arn arn:aws:rds:us-east-1:ACCOUNT_ID:db:clinic-management-db \
  --s3-bucket-name my-backup-bucket \
  --s3-prefix clinic-backups/ \
  --iam-role-arn arn:aws:iam::ACCOUNT_ID:role/rds-export-role
```

## Step 8: Test in Production

### 1. Test Application Locally with RDS
```bash
# Update .env to point to RDS
export DB_HOST="clinic-management-db.xxxxx.us-east-1.rds.amazonaws.com"

# Run your app
php -S localhost:8000

# Test login and CRUD operations
```

### 2. Run Test Suite
```bash
# If you have tests
phpunit tests/
```

### 3. Monitor RDS Performance
```bash
# Check enhanced monitoring
aws rds describe-db-instances \
  --db-instance-identifier clinic-management-db \
  --query 'DBInstances[0].EnableIAMDatabaseAuthentication'

# Check slow queries
mysql -h $MYSQL_HOST -u $MYSQL_USER -p$MYSQL_PASSWORD clinic_management \
  -e "SELECT * FROM mysql.slow_log LIMIT 10;"
```

## Step 9: Decommission SQLite

After confirming everything works:

```bash
# Backup SQLite locally
cp clinic_management.sqlite clinic_management.sqlite.backup

# Remove from production (optional)
# rm clinic_management.sqlite

# Update .gitignore
echo "clinic_management.sqlite" >> .gitignore
echo "clinic_management.sqlite-shm" >> .gitignore
echo "clinic_management.sqlite-wal" >> .gitignore

git add .gitignore
git commit -m "Update .gitignore to exclude SQLite files"
```

## Common Migration Issues & Solutions

### Issue 1: Foreign Key Constraint Error
**Error:** `Foreign key constraint fails`

**Solution:**
```bash
# Disable foreign key checks during migration
mysql -h $MYSQL_HOST -u $MYSQL_USER -p$MYSQL_PASSWORD clinic_management -e "SET FOREIGN_KEY_CHECKS=0;"

# Run insert
# ...

# Re-enable
mysql -h $MYSQL_HOST -u $MYSQL_USER -p$MYSQL_PASSWORD clinic_management -e "SET FOREIGN_KEY_CHECKS=1; REPAIR TABLE appointments;"
```

### Issue 2: Data Type Mismatch
**Error:** `Incorrect integer value`

**Solution:** SQLite treats everything as TEXT. Map properly:
```php
// Convert data types
$value = ($column_type == 'int') ? (int)$value : $value;
$value = ($column_type == 'decimal') ? (float)$value : $value;
```

### Issue 3: Character Encoding Issues
**Error:** `Illegal mix of collations`

**Solution:**
```sql
-- Ensure UTF8MB4 collation
ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Issue 4: Duplicate Key Errors
**Error:** `Duplicate entry for key 'PRIMARY'`

**Solution:**
```bash
# Check for duplicates
mysql -e "SELECT user_id, COUNT(*) FROM clinic_management.users GROUP BY user_id HAVING COUNT(*) > 1;"

# Reset AUTO_INCREMENT
mysql -e "ALTER TABLE users AUTO_INCREMENT=12;"
```

## Rollback Plan

If something goes wrong:

```bash
# Stop using MySQL
# Switch back to SQLite in code

# Restore RDS from snapshot
aws rds restore-db-instance-from-db-snapshot \
  --db-instance-identifier clinic-management-db-restored \
  --db-snapshot-identifier clinic-backup-2026-07-17

# Or delete and recreate from scratch
aws rds delete-db-instance \
  --db-instance-identifier clinic-management-db \
  --skip-final-snapshot
```

## Performance Tuning (After Migration)

### Add Indexes
```sql
-- Add indexes for frequently queried columns
ALTER TABLE appointments ADD INDEX idx_patient_date (patient_id, appointment_date);
ALTER TABLE treatments ADD INDEX idx_appointment (appointment_id);
ALTER TABLE payments ADD INDEX idx_patient_date (patient_id, payment_date);
```

### Enable Query Cache (MySQL 5.7)
```sql
SET GLOBAL query_cache_type = ON;
SET GLOBAL query_cache_size = 268435456;  -- 256MB
```

### Monitor with CloudWatch
```bash
aws cloudwatch get-metric-statistics \
  --namespace AWS/RDS \
  --metric-name CPUUtilization \
  --dimensions Name=DBInstanceIdentifier,Value=clinic-management-db \
  --start-time 2026-07-17T00:00:00Z \
  --end-time 2026-07-18T00:00:00Z \
  --period 3600 \
  --statistics Average,Maximum
```

## Summary

✅ **Steps to Complete:**
1. ✓ Export data from SQLite
2. ✓ Create AWS RDS MySQL instance
3. ✓ Import schema (already MySQL compatible!)
4. ✓ Migrate data using PHP script
5. ✓ Update application configuration
6. ✓ Verify migration
7. ✓ Set up backups
8. ✓ Test in staging/production
9. ✓ Decommission SQLite

Your application is now ready for AWS deployment with MySQL!
