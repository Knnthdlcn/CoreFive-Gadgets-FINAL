#!/usr/bin/env bash
set -e

# Deploy hook: ensure storage symlink and run optional image migration
echo "Running deploy hook..."

php artisan storage:link || true

if [ -f scripts/fix_deployed_images.php ]; then
  echo "Running image migration script (scripts/fix_deployed_images.php)"
  php scripts/fix_deployed_images.php || true
fi

echo "Deploy hook completed."
