# Server Optimization Guide for Workshop Pass Generation

## Problem
When generating passes for a large number of workshop registrations (100+ registrants), the application was experiencing HTTP 500 errors due to server resource limitations.

## Solutions Implemented

### 1. Code-Level Optimizations

#### A. Memory and Execution Time Limits
The `generatePass()` method now includes:
```php
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300'); // 5 minutes
```

#### B. Eager Loading
Implemented eager loading to prevent N+1 query problems:
```php
$registrants = WorkshopRegistration::with([
    'workshop.WorkshopVenueDetail',
    'workshop.conference.society.users',
    'user.userDetail.namePrefix'
])->where(...)->get();
```

#### C. Batch Processing
Added a new `generatePassBatch()` method that processes 50 registrations at a time.
- Access via the "Generate in Batches" option in the UI (appears when >100 registrations)
- Route: `/workshop-registrant/generate-pass-batch/{workshop}?registrant_type=1&batch=1`

### 2. Server Configuration (VPS Settings)

#### PHP Configuration (php.ini)
Update these settings on your VPS:

```ini
; Memory limit - increase for large dataset processing
memory_limit = 512M

; Maximum execution time (seconds)
max_execution_time = 300

; Maximum input time (seconds)
max_input_time = 300

; POST data size limit
post_max_size = 128M

; Upload file size limit
upload_max_filesize = 128M

; Maximum number of input variables
max_input_vars = 5000
```

**Location of php.ini:**
- Ubuntu/Debian: `/etc/php/8.x/fpm/php.ini` and `/etc/php/8.x/cli/php.ini`
- CentOS/RHEL: `/etc/php.ini`

After editing, restart PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm  # Adjust version as needed
```

#### Nginx Configuration
If using Nginx, update these settings in your site config:

```nginx
server {
    # ... other config ...
    
    location ~ \.php$ {
        # Increase timeouts
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
        
        # Increase buffer sizes
        fastcgi_buffer_size 32k;
        fastcgi_buffers 8 16k;
        fastcgi_busy_buffers_size 32k;
        
        # ... other fastcgi settings ...
    }
}
```

Test config and reload:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

#### Apache Configuration
If using Apache, add to `.htaccess` or virtual host config:

```apache
<IfModule mod_php.c>
    php_value memory_limit 512M
    php_value max_execution_time 300
    php_value max_input_time 300
    php_value post_max_size 128M
    php_value upload_max_filesize 128M
</IfModule>
```

Restart Apache:
```bash
sudo systemctl restart apache2
```

### 3. Database Optimization

#### Add Indexes
Ensure proper indexes exist for frequently queried columns:

```sql
-- If not already indexed
CREATE INDEX idx_workshop_registrations_workshop_status 
ON workshop_registrations(workshop_id, registrant_type, status);
```

#### Optimize Tables
Run periodic optimization:
```sql
OPTIMIZE TABLE workshop_registrations;
```

### 4. Usage Instructions

#### For Small to Medium Datasets (< 100 registrations)
Use the regular "Generate for Registered Users" button - this will generate all passes in one go.

#### For Large Datasets (> 100 registrations)
1. Click "Generate in Batches" from the dropdown menu
2. The system will process 50 registrations at a time
3. Navigate through batches using the `?batch=1`, `?batch=2`, etc. parameters

#### For Very Large Datasets (> 500 registrations)
Consider implementing a queued/background job system:
```php
// Future enhancement - dispatch to queue
GenerateWorkshopPassesJob::dispatch($workshop, $registrant_type);
```

### 5. Monitoring

#### Check PHP-FPM Logs
```bash
tail -f /var/log/php8.2-fpm.log
```

#### Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

#### Monitor Server Resources
```bash
# CPU and Memory usage
htop

# Or
top
```

### 6. Alternative Solutions (For Future)

#### A. Queue-Based Processing
Implement Laravel queues with Redis or database driver:
```php
// In controller
dispatch(new GenerateWorkshopPasses($workshop, $registrant_type));
```

#### B. PDF Generation Service
Use a dedicated service like:
- Browserless.io
- DocRaptor
- wkhtmltopdf running on separate server

#### C. Caching
Cache generated QR codes and pass settings:
```php
$qrCode = Cache::remember("qr_{$token}", 3600, function() use ($token) {
    return QrCode::size(120)->generate($url);
});
```

### 7. Testing

Test with different registration counts:
- 10 registrations
- 50 registrations
- 100 registrations
- 200+ registrations

Monitor memory usage and execution time.

### 8. Troubleshooting

#### Still Getting 500 Errors?

1. **Check error logs:**
   ```bash
   tail -50 storage/logs/laravel.log
   tail -50 /var/log/nginx/error.log
   ```

2. **Increase limits further:**
   - Try `memory_limit = 1024M`
   - Try `max_execution_time = 600`

3. **Check server RAM:**
   ```bash
   free -h
   ```
   If RAM is limited, consider upgrading your VPS plan.

4. **Enable error display temporarily:**
   ```php
   // In controller, temporarily add:
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

5. **Use batch processing:**
   Always use the batch option for >100 registrations.

## Summary

The application now handles large datasets more efficiently through:
- ✅ Increased memory and execution limits
- ✅ Eager loading to reduce database queries
- ✅ Batch processing option for very large datasets
- ✅ Optimized queries with proper relationships

Configure your VPS according to the server settings above for optimal performance.
