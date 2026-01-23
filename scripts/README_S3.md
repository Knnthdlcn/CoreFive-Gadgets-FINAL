Configuring S3 for persistent product image storage
==================================================

1) Install the AWS S3 Flysystem adapter (if not already present):

```bash
composer require league/flysystem-aws-s3-v3
```

2) Update `.env` with S3 credentials:

```
FILESYSTEM_DRIVER=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=your_region
AWS_BUCKET=your_bucket
AWS_URL=https://your-bucket.s3.amazonaws.com
```

3) Verify `config/filesystems.php` has the `s3` disk configured (Laravel default includes this). Then set `FILESYSTEM_DRIVER=s3` in production.

4) Update any code that assumes local `storage` symlink (optional):
   - With S3, uploaded files will be stored using `Storage::disk('s3')->putFile('products', $file)`
   - Use `Storage::disk('s3')->url($path)` or the `Storage` helper to generate URLs.

5) Clear config cache and deploy:

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

Notes:
- Using S3 avoids lost files on ephemeral hosts and is recommended for production.
- If switching to S3, remove or update any fallback logic that assumes files are under `/storage` or `/images`.
