# Conference Management API Documentation

## Base URL
```
/api/conference/{slug}
```

Replace `{slug}` with the conference slug (e.g., `international-conference-2025`)

---

## Endpoints

### 1. Get Complete Conference Details
**GET** `/api/conference/{slug}`

Returns complete conference information with all relationships.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "conference_name": "International Conference 2025",
    "slug": "international-conference-2025",
    "conference_theme": "Innovation in Technology",
    "start_date": "2025-01-15",
    "end_date": "2025-01-17",
    "society": {...},
    "ConferenceVenueDetail": {...},
    "ConferenceOrganizer": {...}
  }
}
```

---

### 2. Get Basic Conference Info (Lightweight)
**GET** `/api/conference/{slug}/basic`

Returns only essential conference information for quick loading.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "conference_name": "International Conference 2025",
    "abbreviation": "IC2025",
    "conference_theme": "Innovation in Technology",
    "conference_logo": "path/to/logo.png",
    "conference_banner": "path/to/banner.jpg",
    "start_date": "2025-01-15",
    "end_date": "2025-01-17",
    "slug": "international-conference-2025",
    "primary_color": "#007bff",
    "secendary_color": "#6c757d"
  }
}
```

---

### 3. Get All Conference Data (Complete)
**GET** `/api/conference/{slug}/all`

Returns all conference data in a single request - includes conference details, workshops, hotels, notices, downloads, etc.

**Response:**
```json
{
  "success": true,
  "data": {
    "conference": {...},
    "hotels": [...],
    "workshops": [...],
    "notices": [...],
    "downloads": [...],
    "submission_tracks": [...],
    "article_types": [...],
    "committee_members": [...],
    "official_messages": [...]
  }
}
```

---

### 4. Get About Conference
**GET** `/api/conference/{slug}/about`

Returns conference description, venue, and organizer details.

**Response:**
```json
{
  "success": true,
  "data": {
    "conference_name": "International Conference 2025",
    "conference_description": "...",
    "conference_theme": "Innovation in Technology",
    "start_date": "2025-01-15",
    "end_date": "2025-01-17",
    "venue": {
      "address": "123 Main St",
      "city": "Kathmandu",
      "country": "Nepal"
    },
    "organizer": {
      "name": "...",
      "contact": "..."
    }
  }
}
```

---

### 5. Get Accommodation/Hotels
**GET** `/api/conference/{slug}/accommodation`  
**GET** `/api/conference/{slug}/hotels` (alias)

Returns list of hotels for the conference.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Grand Hotel",
      "address": "123 Hotel St",
      "contact": "+977-1234567",
      "price_range": "$$",
      "display_order": 1
    }
  ]
}
```

---

### 6. Get Workshops
**GET** `/api/conference/{slug}/workshops`

Returns workshops with trainers and venue details.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "AI Workshop",
      "description": "...",
      "start_date": "2025-01-15",
      "workshopVenueDetail": {...},
      "workshopTrainers": [...],
      "workshopChairPersonDetail": {...}
    }
  ]
}
```

---

### 7. Get News & Notices
**GET** `/api/conference/{slug}/news-notices`

Returns news and notices for the conference.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Registration Extended",
      "date": "2025-01-10",
      "description": "...",
      "image": "path/to/image.jpg",
      "is_featured": 1
    }
  ]
}
```

---

### 8. Get Downloads
**GET** `/api/conference/{slug}/downloads`

Returns downloadable files (brochures, schedules, etc.).

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Conference Brochure",
      "file": "path/to/brochure.pdf",
      "is_featured": 1
    }
  ]
}
```

---

### 9. Get Scientific Sessions/Submission Tracks
**GET** `/api/conference/{slug}/scientific-sessions`  
**GET** `/api/conference/{slug}/submission-tracks` (alias)

Returns submission tracks/themes.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Artificial Intelligence",
      "description": "..."
    }
  ]
}
```

---

### 10. Get Article Types
**GET** `/api/conference/{slug}/article-types`

Returns available article/presentation types.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Research Paper"
    },
    {
      "id": 2,
      "name": "Case Study"
    }
  ]
}
```

---

### 11. Get Committee Members
**GET** `/api/conference/{slug}/committee-members`

Returns organizing committee members.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Dr. John Doe",
      "designation": "Conference Chair",
      "user": {...},
      "committeeType": {...},
      "display_order": 1
    }
  ]
}
```

---

### 12. Get Official Messages
**GET** `/api/conference/{slug}/official-messages`

Returns official messages from organizers.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Welcome Message",
      "content": "...",
      "display_order": 1
    }
  ]
}
```

---

### 13. Get Conference Settings
**GET** `/api/conference/{slug}/settings`

Returns conference settings and configurations.

**Response:**
```json
{
  "success": true,
  "data": {
    "registration_enabled": true,
    "submission_enabled": true,
    "show_committee": true
  }
}
```

---

## Error Responses

### Conference Not Found (404)
```json
{
  "success": false,
  "message": "Conference not found"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Error fetching conference",
  "error": "Error details..."
}
```

---

## Usage Examples

### JavaScript/Fetch
```javascript
// Get complete conference data
fetch('/api/conference/international-conference-2025')
  .then(response => response.json())
  .then(data => console.log(data));

// Get only workshops
fetch('/api/conference/international-conference-2025/workshops')
  .then(response => response.json())
  .then(data => console.log(data));

// Get all data at once
fetch('/api/conference/international-conference-2025/all')
  .then(response => response.json())
  .then(data => console.log(data));
```

### PHP/Laravel
```php
use Illuminate\Support\Facades\Http;

$response = Http::get('/api/conference/international-conference-2025');
$conference = $response->json();
```

### cURL
```bash
curl -X GET "http://yourdomain.com/api/conference/international-conference-2025/basic"
```

---

## Best Practices

1. **Use Basic Info** for initial page load to improve performance
2. **Use Specific Endpoints** for individual sections to reduce payload size
3. **Use All Endpoint** when you need complete data and want to minimize requests
4. **Cache Responses** on the client side when appropriate
5. **Handle Errors** gracefully with proper error checking

---

## Performance Tips

- Use `/basic` endpoint for navigation/header data
- Use `/all` endpoint for single-page applications
- Use specific endpoints (like `/workshops`, `/hotels`) for lazy loading sections
- Consider implementing caching on server and client side
