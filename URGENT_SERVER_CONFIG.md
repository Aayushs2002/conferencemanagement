# 🚨 URGENT: Critical Server Configuration for Low-Resource VPS

## Issue
Your VPS is getting HTTP 500 errors with just **20+ registrations**. This indicates severely limited server resources.

## Immediate Actions Required

### 1. Update PHP Configuration (CRITICAL)

```bash
# Find and edit php.ini
sudo nano /etc/php/8.2/fpm/php.ini
```

**Set these values (MINIMUM):**
```ini
memory_limit = 1024M           # Increased to 1GB
max_execution_time = 600       # 10 minutes
max_input_time = 600
post_max_size = 256M
upload_max_filesize = 256M
max_input_vars = 10000

# Additional critical settings
opcache.enable = 1
opcache.memory_consumption = 256
opcache.max_accelerated_files = 20000
```

```bash
# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

### 2. Update PHP-FPM Pool Configuration

```bash
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
```

**Update these values:**
```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 10
pm.max_requests = 500

# Increase request timeout
request_terminate_timeout = 600
```

```bash
sudo systemctl restart php8.2-fpm
```

### 3. Nginx Configuration (If using Nginx)

```bash
sudo nano /etc/nginx/sites-available/medconalert.com
```

**Add/Update these settings:**
```nginx
server {
    # Increase timeouts
    client_max_body_size 256M;
    client_body_timeout 600;
    client_header_timeout 600;
    send_timeout 600;
    
    location ~ \.php$ {
        # Critical timeout settings
        fastcgi_read_timeout 600;
        fastcgi_send_timeout 600;
        fastcgi_connect_timeout 600;
        
        # Increase buffers
        fastcgi_buffer_size 128k;
        fastcgi_buffers 16 128k;
        fastcgi_busy_buffers_size 256k;
        
        # Existing fastcgi settings...
    }
}
```

```bash
sudo nginx -t
sudo systemctl reload nginx
```

## What Changed in Code

### ✅ Automatic Batch Mode
- **>20 registrations**: Automatically redirects to batch mode
- **No manual selection needed**: System detects and switches automatically

### ✅ Smaller Batches
- **Changed from 50 → 10** registrations per batch
- Less memory usage per page load

### ✅ Increased Limits
- **Memory: 512M → 1GB** (1024M)
- **Better for image/QR code generation**

### ✅ Warning Messages
- UI now shows warning when >20 registrations exist
- Recommends using batch mode

## How to Use NOW

### For ANY workshop with >20 registrations:

1. **Click "Generate Pass" dropdown**
2. **Select "Generate in Batches - Recommended"**
3. **System will show 10 passes at a time**
4. **Use Next/Previous buttons to navigate**
5. **Print each batch separately**

### The system will also:
- Auto-redirect to batch mode if you try "Generate for Registered Users" with >20

## Testing After Configuration

```bash
# Test with your largest workshop
# Check logs while testing
tail -f storage/logs/laravel.log

# Monitor memory
watch -n 1 free -h
```

## If Still Getting Errors

### Option 1: Reduce Batch Size Further
Edit controller line:
```php
$perPage = 5; // Instead of 10
```

### Option 2: Upgrade VPS Plan
Your current VPS may have:
- Low RAM (probably < 2GB)
- Limited CPU
- Consider upgrading to higher tier

### Option 3: Check Current Resources

```bash
# Check total RAM
free -h

# Check current PHP-FPM processes
ps aux | grep php-fpm

# Check memory per process
ps aux --sort=-%mem | head -10
```

## Server Requirements Check

Your VPS should have **AT MINIMUM**:
- ✅ 2GB RAM (4GB recommended)
- ✅ 2 CPU cores
- ✅ PHP 8.1+
- ✅ Nginx or Apache with proper configuration

If you have less than 2GB RAM, you MUST:
1. Use batch mode for ALL passes (even <20)
2. Reduce batch size to 5
3. Consider upgrading VPS

## Quick Fix Commands

```bash
# 1. Navigate to project
cd /path/to/conferencemanagement

# 2. Pull latest changes
git pull

# 3. Clear all caches
php artisan optimize:clear

# 4. Update PHP config
sudo nano /etc/php/8.2/fpm/php.ini
# Set memory_limit = 1024M
# Set max_execution_time = 600

# 5. Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx

# 6. Test immediately
```

## Priority Actions (RIGHT NOW)

1. ⚠️ **Update php.ini** - Set memory_limit = 1024M
2. ⚠️ **Restart PHP-FPM** - `sudo systemctl restart php8.2-fpm`
3. ⚠️ **Deploy code changes** - `git pull`
4. ⚠️ **Clear Laravel cache** - `php artisan optimize:clear`
5. ⚠️ **Test with batch mode** - Use "Generate in Batches"

## Support

If errors persist after all configurations:
```bash
# Get exact error from logs
tail -50 storage/logs/laravel.log

# Check PHP errors
sudo tail -50 /var/log/php8.2-fpm.log

# Share these errors for further diagnosis
```

---

**⚠️ CRITICAL: Your VPS is resource-constrained. Follow ALL steps above immediately.**
