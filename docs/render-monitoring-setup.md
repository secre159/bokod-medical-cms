# Render Sleep Prevention - Monitoring Setup Guide

## UptimeRobot Setup (Free - Recommended)

### Step 1: Create UptimeRobot Account
1. Go to https://uptimerobot.com/
2. Sign up for free account (50 monitors free)
3. Verify your email

### Step 2: Add Monitor
1. Click "Add New Monitor"
2. Monitor Type: HTTP(s)
3. Friendly Name: "CMS Health Check"
4. URL: https://your-cms-app.onrender.com/
5. Monitoring Interval: 5 minutes (free tier)
6. Click "Create Monitor"

### Alternative Monitoring Services:
- **Pingdom** (free tier with ads)
- **StatusCake** (free with limited features)
- **Freshping** (50 checks free)

## Health Check Endpoint (Optional)

Create a lightweight health check endpoint to reduce server load:

### Route (add to web.php):
```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'service' => 'CMS Health Check'
    ]);
});
```

Then monitor: https://your-cms-app.onrender.com/health

## Cron Job Services (Alternative)

### EasyCron (Free tier)
1. Sign up at https://www.easycron.com/
2. Create cron job with your site URL
3. Set to run every 10-15 minutes

### cron-job.org (Free)
1. Register at https://cron-job.org/
2. Add new cronjob
3. Set URL and interval (every 15 minutes)

## Important Notes:
- Free monitoring services may have limits
- Don't ping too frequently (respect Render's resources)
- Monitor essential pages only, not every endpoint
- Consider upgrading to Render Pro for production use