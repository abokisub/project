# Logo Setup Instructions

## Where to Place Your Logo

Place your main logo file in the following directory:

```
public/images/logo.png
```

Or alternatively:
- `public/images/logo.svg` (recommended for scalability)
- `public/images/logo.jpg`

## Supported Formats

- **PNG** (recommended for logos with transparency)
- **SVG** (recommended for scalability)
- **JPG/JPEG** (if you don't have PNG/SVG)

## File Naming

The system will automatically look for your logo in this order:
1. `public/images/logo.png`
2. `public/images/logo.svg`
3. `public/images/logo.jpg`

If none of these files exist, it will display a placeholder "K" logo.

## Logo Specifications

### Recommended Sizes:
- **Main Logo**: 200x200px to 400x400px (square or rectangular)
- **Format**: PNG with transparency or SVG
- **File Size**: Keep under 500KB for optimal performance

### Logo Placement Steps:

1. **Copy your logo file** to:
   ```
   C:\Users\Habukhan\Documents\kobo\public\images\logo.png
   ```

2. **Or use command line**:
   ```bash
   # Copy your logo file to the images directory
   copy "path\to\your\logo.png" "public\images\logo.png"
   ```

3. **The logo will automatically appear** in:
   - Admin login page (`/secure/app`)
   - Admin dashboard header
   - All admin pages

## Custom Logo Path

If you want to use a different filename or path, you can update the views:

**File**: `resources/views/admin/auth/login.blade.php`
**File**: `resources/views/admin/dashboard.blade.php`

Change:
```php
{{ asset('images/logo.png') }}
```

To your custom path:
```php
{{ asset('images/your-logo-name.png') }}
```

## Testing

After placing your logo:

1. Clear Laravel cache (if needed):
   ```bash
   php artisan view:clear
   php artisan cache:clear
   ```

2. Visit the admin login page:
   ```
   http://localhost:8000/secure/app
   ```

3. Your logo should appear automatically!

## Notes

- The logo will be automatically resized to fit the design
- SVG logos will scale perfectly at any size
- PNG logos should have a transparent background for best results
- The system falls back to a placeholder if the logo file is not found

