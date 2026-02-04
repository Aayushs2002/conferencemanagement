# ⚡ QUICK START - Fix HTTP 500 Error (>20 Registrations)

## What Changed
- ✅ System now handles **10 registrations per batch** (reduced from 50)
- ✅ **Auto-redirects** to batch mode when >20 registrations
- ✅ Warning shown in UI to use batch mode
- ✅ Memory increased to 1GB

## Deploy Code (2 minutes)

```bash
cd /path/to/conferencemanagement
git pull
php artisan optimize:clear
```

## Update Server Config (3 minutes)

### Step 1: Edit php.ini
```bash
sudo nano /etc/php/8.2/fpm/php.ini
```

Change these lines:
```ini
memory_limit = 1024M
max_execution_time = 600
```

Save: `Ctrl+X` → `Y` → `Enter`

### Step 2: Restart PHP
```bash
sudo systemctl restart php8.2-fpm
```

### Step 3: Update Nginx (if using)
```bash
sudo nano /etc/nginx/sites-available/medconalert.com
```

Add in `location ~ \.php$` section:
```nginx
fastcgi_read_timeout 600;
fastcgi_buffer_size 128k;
fastcgi_buffers 16 128k;
```

Save and reload:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

## Test Now

1. Go to workshop with >20 registrations
2. Click "Generate Pass" → "Generate in Batches - Recommended"  
3. Should see 10 passes at a time
4. Use Next/Previous buttons to navigate
5. Print each batch

## Still Getting Error?

### Check logs:
```bash
tail -50 storage/logs/laravel.log
```

### Check RAM:
```bash
free -h
```

If you have **less than 2GB RAM**, reduce batch size to 5:
```bash
nano app/Http/Controllers/Backend/Workshop/WorkshopRegistration/WorkshopRegistrationController.php
```

Find line with `$perPage = 10;` and change to:
```php
$perPage = 5;
```

## Important Notes

- ⚠️ **ALWAYS use "Generate in Batches"** for >20 registrations
- ⚠️ System will **auto-redirect** if you forget
- ⚠️ Each batch processes **10 passes** (can be reduced to 5 if needed)

## Files Changed
1. `WorkshopRegistrationController.php` - Auto-redirect + smaller batches
2. `index.blade.php` - Warning + batch option at 20 threshold
3. `workshop-trainer/index.blade.php` - Same for trainers

---

**Ready? Deploy code → Update config → Test → Done! ✅**
