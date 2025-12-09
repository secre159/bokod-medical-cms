# External Database Configuration

This document explains how to configure the application to work with databases hosted on different Render accounts or external database services.

## Overview

The application now supports two database configuration modes:
1. **Default Database** - Same Render account or local database
2. **External Database** - Database on a different Render account or external service

## Configuration Options

### Option 1: Using Environment Variables (Recommended)

Add these environment variables to your Render web service:

#### For External MySQL Database:
```env
# Use external MySQL connection
DB_CONNECTION=mysql_external

# External database credentials
EXTERNAL_DB_HOST=your-external-mysql-host.render.com
EXTERNAL_DB_PORT=3306
EXTERNAL_DB_DATABASE=your_database_name
EXTERNAL_DB_USERNAME=your_username
EXTERNAL_DB_PASSWORD=your_password

# Or use a full connection URL
EXTERNAL_DB_URL=mysql://username:password@host:port/database
```

#### For External PostgreSQL Database:
```env
# Use external PostgreSQL connection
DB_CONNECTION=pgsql_external

# External database credentials
EXTERNAL_DB_HOST=your-external-postgres-host.render.com
EXTERNAL_DB_PORT=5432
EXTERNAL_DB_DATABASE=your_database_name
EXTERNAL_DB_USERNAME=your_username
EXTERNAL_DB_PASSWORD=your_password

# Or use a full connection URL (Render provides this)
EXTERNAL_DB_URL=postgres://username:password@host:port/database
```

### Option 2: Switch Between Databases Dynamically

You can switch between the default and external database by changing the `DB_CONNECTION` variable:

```env
# Use default database (same Render account)
DB_CONNECTION=mysql
# or
DB_CONNECTION=pgsql

# Use external database (different Render account)
DB_CONNECTION=mysql_external
# or
DB_CONNECTION=pgsql_external
```

## Setup Steps for Different Render Accounts

### Step 1: Create External Database on Render

1. Log into your **second Render account**
2. Create a new PostgreSQL or MySQL database
3. Copy the **Internal Database URL** or individual credentials from Render dashboard

### Step 2: Configure Your Web Service

1. Go to your **first Render account** (where your web service is deployed)
2. Navigate to your web service → Environment
3. Add the external database environment variables:

**If using full connection URL:**
```env
DB_CONNECTION=pgsql_external
EXTERNAL_DB_URL=postgres://user:pass@dpg-xxxxx-a.oregon-postgres.render.com/dbname
```

**If using individual credentials:**
```env
DB_CONNECTION=pgsql_external
EXTERNAL_DB_HOST=dpg-xxxxx-a.oregon-postgres.render.com
EXTERNAL_DB_PORT=5432
EXTERNAL_DB_DATABASE=dbname
EXTERNAL_DB_USERNAME=username
EXTERNAL_DB_PASSWORD=password
```

### Step 3: Run Migrations (if needed)

After deploying, run migrations to set up the database schema:

```bash
php artisan migrate
```

## Testing the Configuration

You can test your database connection:

```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo();

# Test external connection specifically
>>> DB::connection('mysql_external')->getPdo();
# or
>>> DB::connection('pgsql_external')->getPdo();
```

## Using External Database in Code

If you need to explicitly use the external database in your code:

```php
// Query using external database
$users = DB::connection('mysql_external')->table('users')->get();

// Model using external database
class ExternalUser extends Model
{
    protected $connection = 'mysql_external';
    protected $table = 'users';
}
```

## Fallback Behavior

If external database credentials are not provided, the system will automatically fall back to the default database configuration:

- `EXTERNAL_DB_HOST` falls back to `DB_HOST`
- `EXTERNAL_DB_PORT` falls back to `DB_PORT`
- `EXTERNAL_DB_DATABASE` falls back to `DB_DATABASE`
- `EXTERNAL_DB_USERNAME` falls back to `DB_USERNAME`
- `EXTERNAL_DB_PASSWORD` falls back to `DB_PASSWORD`

This ensures the application works seamlessly in both scenarios.

## Common Issues and Solutions

### Issue 1: Connection Timeout
**Solution:** Increase the timeout value:
```env
DB_TIMEOUT=60
```

### Issue 2: SSL Certificate Error
**Solution:** For PostgreSQL, adjust the SSL mode:
```env
DB_SSLMODE=require
# or
DB_SSLMODE=prefer
```

### Issue 3: Cannot Connect from Different Render Account
**Solution:** 
- Ensure you're using the **Internal Database URL** (not External)
- Verify the database allows connections from your web service IP
- Check Render's network policies between services

## Security Best Practices

1. **Never commit credentials** to your repository
2. **Use Render's environment variables** for all sensitive data
3. **Rotate passwords regularly** for external databases
4. **Use SSL connections** when available
5. **Limit database user permissions** to only what's needed

## Monitoring

Monitor your database connections:

```bash
# Check active connections
php artisan db:show

# Monitor query performance
php artisan db:monitor
```

## Support

For issues:
1. Check Render service logs
2. Verify environment variables are set correctly
3. Test database connectivity from Render Shell
4. Review Laravel logs: `storage/logs/laravel.log`
