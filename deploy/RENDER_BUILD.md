Render Build & Deploy Guide
==========================

Use these instructions to configure the Render web service so build steps run in the build phase (recommended) and the runtime start command only starts services.

1) Build Command (set in Render service settings)

```bash
# PHP deps
composer install --no-dev --optimize-autoloader --no-interaction

# Build frontend assets
npm ci --silent && npm run build --silent || true

# Run any repository-specific deploy helpers (copy storage -> public/images etc)
composer run render-deploy || true
```

Notes:
- Run `composer install` and `npm ci` in the Build Command so the runtime image doesn't need package managers present.
- `composer run render-deploy` runs `php artisan storage:link` and `php scripts/fix_deployed_images.php` (if present) to populate `public/images` from `storage/app/public/products` and normalize DB paths.

2) Start Command (runtime)

Use a minimal start command that does not attempt to install packages. The repository includes `scripts/start.sh` which is resilient if `npm` / `composer` are missing. Example start command:

```bash
/bin/bash ./scripts/start.sh
```

3) Troubleshooting

- If deploy logs show `npm: command not found` or `composer: command not found`, that means you attempted to run build steps in the start phase. Move build steps to the Build Command.
- If you need persistent uploads, configure an external object store (S3) and set `FILESYSTEM_DISK=s3` and appropriate env vars.
- If `composer run render-deploy` fails during build, check that `storage/app/public/products` contains files (uploads) or re-upload images via admin after deploy.

4) Quick verification after deploy

- Visit `https://<your-service>/` and confirm product images load.
- If images 404, run the admin diagnostic endpoint (sign in as admin):
  `/admin/diagnose-image?file=<filename>`

