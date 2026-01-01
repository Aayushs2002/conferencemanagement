# API Image/File URL Mapping

## Overview
The API now returns full URLs for all images and files using Laravel's `Storage::url()` helper with the correct folder structure.

## Folder Structure Mapping

### Conference
- **conference_logo**: `storage/app/public/conference/conference/logo/[filename]`
- **conference_banner**: `storage/app/public/conference/conference/banner/[filename]`
- **partner_logos**: `storage/app/public/conference/partner-logos/[filename]`

### Hotels/Accommodation
- **featured_image**: `storage/app/public/hotel/featured-image/[filename]`
- **cover_image**: `storage/app/public/hotel/cover-image/[filename]`
- **images**: `storage/app/public/hotel/images/[filename]` (JSON array)

### Workshops
- **banner**: `storage/app/public/workshop/workshop/banner/[filename]`
- **image**: `storage/app/public/workshop/workshop/image/[filename]`
- **featured_image**: `storage/app/public/workshop/workshop/featured-image/[filename]`

### Notices
- **image**: `storage/app/public/notice/image/[filename]`
- **attachment**: `storage/app/public/notice/attachment/[filename]`

### Downloads
- **file**: `storage/app/public/download/file/[filename]`
- **image**: `storage/app/public/download/image/[filename]`
- **thumbnail**: `storage/app/public/download/thumbnail/[filename]`

### Committee Members
- **image**: `storage/app/public/committee/image/[filename]`
- **photo**: `storage/app/public/committee/photo/[filename]`

### Official Messages
- **image**: `storage/app/public/offical-message/image/[filename]`
- **photo**: `storage/app/public/offical-message/photo/[filename]`
- **attachment**: `storage/app/public/offical-message/attachment/[filename]`

## How It Works

### Helper Method: `formatMediaUrls()`
```php
private function formatMediaUrls($item, $fieldMappings)
{
    // $fieldMappings = ['field_name' => 'folder/path/']
    // Automatically handles arrays and JSON objects
}
```

### Example Usage
```php
$conference = $this->formatMediaUrls($conference, [
    'conference_logo' => 'conference/conference/logo/',
    'conference_banner' => 'conference/conference/banner/',
    'partner_logos' => 'conference/partner-logos/'
]);
```

### Output Format
**Before:**
```json
{
    "conference_logo": "logo123.jpg"
}
```

**After:**
```json
{
    "conference_logo": "http://yourdomain.com/storage/conference/conference/logo/logo123.jpg"
}
```

### Array Handling
For JSON array fields like `partner_logos` or hotel `images`:

**Before:**
```json
{
    "partner_logos": ["logo1.jpg", "logo2.jpg"]
}
```

**After:**
```json
{
    "partner_logos": [
        "http://yourdomain.com/storage/conference/partner-logos/logo1.jpg",
        "http://yourdomain.com/storage/conference/partner-logos/logo2.jpg"
    ]
}
```

### JSON Object Array Handling
For hotel images stored as JSON with `fileName` key:

**Before:**
```json
{
    "images": [
        {"fileName": "room1.jpg", "caption": "Deluxe Room"},
        {"fileName": "room2.jpg", "caption": "Suite"}
    ]
}
```

**After:**
```json
{
    "images": [
        "http://yourdomain.com/storage/hotel/images/room1.jpg",
        "http://yourdomain.com/storage/hotel/images/room2.jpg"
    ]
}
```

## API Endpoints with Image URLs

All the following endpoints return properly formatted image/file URLs:

1. **GET** `/api/conference/{slug}` - Full conference with all images
2. **GET** `/api/conference/{slug}/basic` - Basic info with logos
3. **GET** `/api/conference/{slug}/all` - Complete data with all media
4. **GET** `/api/conference/{slug}/accommodation` - Hotels with images
5. **GET** `/api/conference/{slug}/workshops` - Workshops with images
6. **GET** `/api/conference/{slug}/news-notices` - Notices with attachments
7. **GET** `/api/conference/{slug}/downloads` - Files with full URLs
8. **GET** `/api/conference/{slug}/committee-members` - Members with photos
9. **GET** `/api/conference/{slug}/official-messages` - Messages with images

## Important Notes

1. **Storage Link**: Ensure `php artisan storage:link` has been run to create the symbolic link from `public/storage` to `storage/app/public`

2. **URL Generation**: Uses Laravel's `Storage::url()` which automatically prepends the correct domain and path

3. **Already Full URLs**: If a URL is already complete (has http/https), it's returned as-is

4. **Null Safety**: Empty or null values are handled gracefully and returned as null

5. **Frontend Usage**: Frontend can directly use these URLs in `<img>` tags without any concatenation:
   ```html
   <img src="{{ image_url }}" alt="Conference Logo">
   ```
