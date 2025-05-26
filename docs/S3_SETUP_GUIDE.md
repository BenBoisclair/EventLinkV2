# S3 Setup Guide for EventLink

## S3 Bucket Configuration

### 1. Create S3 Bucket
- Bucket name: `eventlinkdev` (or your chosen name)
- Region: `ap-southeast-1` (or your preferred region)

### 2. Set Object Ownership
- Go to Bucket → Permissions → Object Ownership
- Select: **Bucket owner enforced**
- This disables ACLs (recommended for security)

### 3. Configure Bucket Policy
Add this policy under Bucket → Permissions → Bucket Policy:
```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicReadForAllObjects",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::YOUR_BUCKET_NAME/*"
        }
    ]
}
```

### 4. Configure CORS (Optional)
Add under Bucket → Permissions → CORS:
```json
[
    {
        "AllowedHeaders": ["*"],
        "AllowedMethods": ["GET", "PUT", "POST", "DELETE"],
        "AllowedOrigins": ["*"],
        "ExposeHeaders": ["ETag", "Content-Type", "Content-Length", "x-amz-request-id"],
        "MaxAgeSeconds": 3000
    }
]
```

## Laravel Configuration

### 1. Environment Variables (.env)
```
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=eventlinkdev
AWS_URL=https://eventlinkdev.s3.ap-southeast-1.amazonaws.com
```

### 2. Filesystem Configuration (config/filesystems.php)
```php
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
    'url' => env('AWS_URL'),
    'endpoint' => env('AWS_ENDPOINT'),
    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    'throw' => true,
    'report' => true,
],
```

### 3. Upload Code Example
```php
// Simple upload without ACL
Storage::disk('s3')->put($path, $fileContent);

// Upload with options (no ACL needed)
Storage::disk('s3')->putFileAs(
    $directory,
    $file,
    $filename,
    [
        'ContentType' => $file->getMimeType(),
        'CacheControl' => 'public, max-age=31536000',
    ]
);
```

## Key Points
- **No ACL parameters needed** when Object Ownership is "Bucket owner enforced"
- Files are public through the **bucket policy**, not individual ACLs
- This is the **recommended security configuration** for S3