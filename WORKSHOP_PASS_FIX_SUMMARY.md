# Workshop Pass Generation - HTTP 500 Error Fix (UPDATED FOR LOW-RESOURCE VPS)

## Problem Summary
When generating passes for workshop registrations on the live VPS server, the application returns HTTP 500 error. **Critical: Server has issues with just 20+ registrations**, indicating severely limited resources.

## Root Cause
The error occurs due to:
1. **Memory Exhaustion** - PHP runs out of allocated memory even with moderate datasets
2. **Execution Timeout** - Script exceeds max_execution_time limit
3. **N+1 Query Problems** - Multiple database queries loading related data
4. **Severely Constrained VPS** - Very limited RAM/CPU resources

## Solutions Implemented

### 1. Application Code Changes

#### File: `WorkshopRegistrationController.php`
- ✅ Increased memory to **1GB (1024M)** from 512M
- ✅ Extended execution time to **600 seconds** (10 minutes)
- ✅ **Auto-redirect to batch mode** when >20 registrations
- ✅ Implemented eager loading to prevent N+1 queries
- ✅ Batch processing reduced to **10 registrations per batch** (was 50)

#### Files: `index.blade.php` (workshop-registration & workshop-trainer)
- ✅ Added "Generate in Batches" option (appears when **>20** registrations)
- ✅ Shows warning message to use batch mode
- ✅ Displays count of registrations

#### File: `registrant-pass.blade.php`
- ✅ Added batch navigation UI
- ✅ Shows current batch number and total batches
- ✅ Print button for each batch

#### File: `routes/web/conference.php`
- ✅ Added route for batch generation: `workshop.generatePassBatch`

### 2. Server Configuration Required ⚠️ CRITICAL

You MUST update your VPS server configuration immediately. Follow:
- **`URGENT_SERVER_CONFIG.md`** - Critical configuration steps
- **`VPS_SETUP_INSTRUCTIONS.md`** - Quick setup guide

**Minimum server changes needed:**
```ini
memory_limit = 1024M              # 1GB (was 512M)
max_execution_time = 600          # 10 minutes (was 300)
pm.max_children = 50              # PHP-FPM setting
```

## How to Use NOW

### For ANY Dataset
The system now **automatically handles** the switching:

1. Click "Generate Pass" dropdown
2. If **≤20 registrations**: System generates all at once
3. If **>20 registrations**: System auto-redirects to batch mode OR you can manually select "Generate in Batches - Recommended"

### Batch Mode Features
- Processes **10 passes at a time**
- Navigation buttons (Previous/Next batch)
- Shows "Batch X of Y"
- Print button for each batch

### Example URLs
```
# Auto-handles based on count
/workshop-registrant/generate-pass/{workshop}?registrant_type=1

# Manual batch mode
/workshop-registrant/generate-pass-batch/{workshop}?registrant_type=1&batch=1
```

## Testing Checklist

Before deploying to production:
- [x] Test with 5 registrations (should work)
- [x] Test with 15 registrations (should work)
- [x] Test with 25 registrations (should auto-redirect to batch mode)
- [x] Test with 50+ registrations (use batch mode)
- [ ] Verify server configuration changes applied
- [ ] Check PHP-FPM and Nginx restarted
- [ ] Monitor Laravel logs during generation
- [ ] Test batch navigation works
- [ ] Verify each batch prints correctly

## Files Modified

1. `app/Http/Controllers/Backend/Workshop/WorkshopRegistration/WorkshopRegistrationController.php`
   - Updated `generatePass()` method
   - Added `generatePassBatch()` method

2. `resources/views/backend/workshop/workshop-registration/index.blade.php`
   - Added batch generation option to dropdown

3. `resources/views/backend/workshop/workshop-trainer/index.blade.php`
   - Added batch generation option to dropdown

4. `resources/views/backend/workshop/pass/registrant-pass.blade.php`
   - Added batch navigation UI
   - Added print button

5. `routes/web/conference.php`
   - Added batch generation route

## New Files Created

1. `SERVER_OPTIMIZATION_GUIDE.md` - Comprehensive documentation
2. `VPS_SETUP_INSTRUCTIONS.md` - Quick setup guide
3. `WORKSHOP_PASS_FIX_SUMMARY.md` - This file

## Next Steps

1. **Deploy the code changes** to your VPS
   ```bash
   git pull origin main
   php artisan route:clear
   php artisan view:clear
   ```

2. **Update server configuration** following `VPS_SETUP_INSTRUCTIONS.md`

3. **Test thoroughly** with different registration counts

4. **Monitor** the first few large pass generations

## Monitoring Commands

```bash
# Watch Laravel logs
tail -f storage/logs/laravel.log

# Watch Nginx error logs
sudo tail -f /var/log/nginx/error.log

# Check memory usage
free -h

# Check PHP processes
ps aux | grep php-fpm
```

## Fallback Plan

If issues persist even after optimization:
1. Use batch generation for ALL datasets (modify threshold from 100 to 50)
2. Implement Laravel Queue system for background processing
3. Consider upgrading VPS plan for more RAM
4. Split pass generation into PDF files (generate PDFs in batches, combine later)

## Support

If you encounter issues:
1. Check `storage/logs/laravel.log` for error details
2. Verify server configuration is applied correctly
3. Ensure all services (PHP-FPM, Nginx) are restarted
4. Test with progressively larger datasets to find breaking point

---

**Author:** GitHub Copilot  
**Date:** February 4, 2026  
**Priority:** High - Production Issue
