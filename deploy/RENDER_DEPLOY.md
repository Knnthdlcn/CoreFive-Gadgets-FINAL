Render deploy notes
===================

Add the following commands to your Render service build/start steps to ensure uploaded images are available and the `storage` symlink exists.

Build command (example):

```bash
# install PHP deps
composer install --no-interaction --prefer-dist --optimize-autoloader

# install node deps + build assets (if used)
npm ci
npm run build

# create storage symlink
php artisan storage:link || true

# run any image migration script (optional)
php scripts/fix_deployed_images.php || true
```

Start command (example):

```bash
# typical PHP start on Render when using a Docker or web service
php artisan config:cache || true
php artisan route:cache || true
# start PHP-FPM / web server as configured by Render image
```

If your Render plan provides a persistent volume, ensure uploaded image files persist there. If not, use S3 (see scripts/README_S3.md) for permanent storage.
