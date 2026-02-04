# Deployment Checklist - Workshop Pass Generation Fix

## Pre-Deployment

- [ ] Review all code changes
- [ ] Test locally if possible
- [ ] Backup database
- [ ] Backup current code

## Code Deployment

```bash
# 1. Navigate to project directory
cd /path/to/conferencemanagement

# 2. Pull latest changes
git pull origin main  # or your branch name

# 3. Clear caches
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear

# 4. Verify files are updated
ls -la app/Http/Controllers/Backend/Workshop/WorkshopRegistration/
cat app/Http/Controllers/Backend/Workshop/WorkshopRegistration/WorkshopRegistrationController.php | grep "generatePassBatch"
```

## Server Configuration

### Step 1: Update PHP Configuration

```bash
# Find php.ini location
php --ini

# Edit php.ini (adjust path based on your PHP version)
sudo nano /etc/php/8.2/fpm/php.ini
```

Update these values:
```ini
memory_limit = 512M
max_execution_time = 300
max_input_time = 300
post_max_size = 128M
upload_max_filesize = 128M
max_input_vars = 5000
```

Save and exit (Ctrl+X, Y, Enter)

```bash
# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Verify restart was successful
sudo systemctl status php8.2-fpm
```

### Step 2: Update Nginx (if applicable)

```bash
# Edit site config
sudo nano /etc/nginx/sites-available/medconalert.com
```

Add inside `location ~ \.php$` block:
```nginx
fastcgi_read_timeout 300;
fastcgi_send_timeout 300;
fastcgi_buffer_size 32k;
fastcgi_buffers 8 16k;
fastcgi_busy_buffers_size 32k;
```

```bash
# Test config
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx

# Verify reload was successful
sudo systemctl status nginx
```

## Testing

### Test 1: Small Dataset (5-10 registrations)
- [ ] Navigate to workshop with few registrations
- [ ] Click "Generate Pass" → "Generate for Registered Users"
- [ ] Verify passes generate successfully
- [ ] Check passes display correctly

### Test 2: Medium Dataset (50-80 registrations)
- [ ] Navigate to workshop with moderate registrations
- [ ] Click "Generate Pass" → "Generate for Registered Users"
- [ ] Verify passes generate (may take 30-60 seconds)
- [ ] Check passes display correctly

### Test 3: Large Dataset (>100 registrations)
- [ ] Navigate to workshop with many registrations
- [ ] Verify "Generate in Batches" option appears in dropdown
- [ ] Click "Generate in Batches"
- [ ] Verify batch navigation appears (Batch 1 of X)
- [ ] Test "Next Batch" button
- [ ] Test "Previous Batch" button
- [ ] Test "Print This Batch" button
- [ ] Verify all passes display correctly

### Test 4: Monitor Logs
```bash
# In one terminal, watch Laravel logs
tail -f storage/logs/laravel.log

# In another terminal, watch error logs
sudo tail -f /var/log/nginx/error.log
```

## Monitoring After Deployment

### First Hour
- [ ] Check logs every 15 minutes
- [ ] Monitor server resources: `htop` or `top`
- [ ] Test pass generation with different datasets

### First Day
- [ ] Check logs every few hours
- [ ] Monitor for any 500 errors
- [ ] Gather user feedback

### Commands for Monitoring

```bash
# Watch Laravel logs in real-time
tail -f storage/logs/laravel.log

# Check for recent errors
tail -100 storage/logs/laravel.log | grep -i error

# Check Nginx error log
sudo tail -50 /var/log/nginx/error.log

# Monitor server resources
htop
# or
top

# Check memory usage
free -h

# Check disk space
df -h

# Check PHP-FPM processes
ps aux | grep php-fpm | wc -l
```

## Rollback Plan (If Issues Occur)

```bash
# 1. Revert code changes
git reset --hard HEAD~1  # Go back one commit
# or
git checkout <previous-commit-hash>

# 2. Clear caches again
php artisan route:clear
php artisan view:clear

# 3. Revert server config if needed
# (restore backup of php.ini and nginx config)

# 4. Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
```

## Success Criteria

- [x] Code deployed without errors
- [x] Server configuration updated
- [x] Services restarted successfully
- [ ] Small dataset test passes
- [ ] Medium dataset test passes  
- [ ] Large dataset test passes (with batch option)
- [ ] Batch navigation works correctly
- [ ] No 500 errors in logs
- [ ] Server resources within normal limits

## Support Contact

If issues persist:
1. Check all logs thoroughly
2. Verify configuration is correct
3. Test with progressively larger datasets
4. Document any new errors in Laravel logs
5. Check server RAM: `free -h`

## Documentation References

- Full guide: `SERVER_OPTIMIZATION_GUIDE.md`
- Quick setup: `VPS_SETUP_INSTRUCTIONS.md`
- Summary: `WORKSHOP_PASS_FIX_SUMMARY.md`

---

**Deployment Date:** _______________  
**Deployed By:** _______________  
**All Tests Passed:** ☐ Yes  ☐ No  
**Issues Encountered:** _______________________________________________
