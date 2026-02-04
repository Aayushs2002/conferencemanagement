# Quick VPS Setup Instructions

## Step 1: Update PHP Configuration

### Find your php.ini file:
```bash
php --ini
```

This will show you the path to your php.ini file. Edit it:

```bash
sudo nano /etc/php/8.2/fpm/php.ini
```

### Update these values:
```ini
memory_limit = 512M
max_execution_time = 300
max_input_time = 300
post_max_size = 128M
upload_max_filesize = 128M
```

Save with `Ctrl + X`, then `Y`, then `Enter`.

### Restart PHP-FPM:
```bash
# For PHP 8.2
sudo systemctl restart php8.2-fpm

# Check status
sudo systemctl status php8.2-fpm
```

## Step 2: Update Nginx Configuration (if using Nginx)

Edit your site configuration:
```bash
sudo nano /etc/nginx/sites-available/medconalert.com
```

Add these lines inside the `location ~ \.php$` block:
```nginx
fastcgi_read_timeout 300;
fastcgi_send_timeout 300;
fastcgi_buffer_size 32k;
fastcgi_buffers 8 16k;
```

Test and reload:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

## Step 3: Clear Laravel Cache

```bash
cd /path/to/your/conferencemanagement
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Step 4: Test

1. Try generating passes for a small number of registrations first
2. If successful, try with larger numbers
3. For >100 registrations, use the "Generate in Batches" option

## Step 5: Monitor

Watch for errors:
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Nginx error log
sudo tail -f /var/log/nginx/error.log

# PHP-FPM log
sudo tail -f /var/log/php8.2-fpm.log
```

## Troubleshooting

### Still getting 500 error?

1. **Increase memory further:**
   ```bash
   memory_limit = 1024M  # or even 2048M
   ```

2. **Check server RAM:**
   ```bash
   free -h
   ```
   
3. **Use batch processing:**
   Always use "Generate in Batches" for >100 registrations

4. **Check file permissions:**
   ```bash
   sudo chown -R www-data:www-data storage/
   sudo chmod -R 775 storage/
   ```

### Need more help?
Check the full guide: `SERVER_OPTIMIZATION_GUIDE.md`
