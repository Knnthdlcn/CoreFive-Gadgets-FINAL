#!/usr/bin/env bash
set -e

cd /var/www/html


if command -v npm >/dev/null 2>&1; then
  echo "npm found, running frontend install/build..."
  npm ci --silent && npm run build --silent || true
else
  echo "npm not found; attempting to install Node via nvm..."
  # Attempt to install nvm + node (best-effort). This requires curl/bash and internet access.
  if command -v curl >/dev/null 2>&1 && command -v bash >/dev/null 2>&1; then
    export NVM_DIR="$HOME/.nvm"
    if [ ! -d "$NVM_DIR" ]; then
      echo "Installing nvm into $NVM_DIR"
      curl -fsSL https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.3/install.sh | bash || true
    fi
    # shellcheck source=/dev/null
    [ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh"
    if command -v nvm >/dev/null 2>&1; then
      echo "nvm available; installing latest LTS node..."
      nvm install --lts || true
      nvm use --lts || true
    fi
    if command -v npm >/dev/null 2>&1; then
      echo "npm installed; running frontend build"
      npm ci --silent && npm run build --silent || true
    else
      echo "npm still not available after nvm attempt; skipping frontend build"
    fi
  else
    echo "curl or bash not found; cannot install node. Skipping frontend build."
  fi
fi

if command -v composer >/dev/null 2>&1; then
  echo "composer found, running render-deploy script..."
  composer run render-deploy || true
else
  echo "composer not found; attempting to install Composer (best-effort)..."
  # Try installing composer via official installer if php and curl are available
  if command -v php >/dev/null 2>&1 && command -v curl >/dev/null 2>&1; then
    EXPECTED_SIG=$(curl -s https://composer.github.io/installer.sig || true)
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" || true
    php composer-setup.php --quiet --install-dir=/usr/local/bin --filename=composer || php composer-setup.php --quiet --install-dir=/usr/bin --filename=composer || true
    php -r "if (file_exists('composer-setup.php')) unlink('composer-setup.php');" || true
  fi
  if command -v composer >/dev/null 2>&1; then
    echo "composer installed; running render-deploy script..."
    composer run render-deploy || true
  else
    echo "composer not available after installer attempt; skipping render-deploy. Install composer in build image or run `composer run render-deploy` during deploy."
  fi
fi


php artisan storage:link || true
php scripts/fix_deployed_images.php || true
php artisan migrate --force || true


mkdir -p storage/app/public/products
mkdir -p storage/app/public/avatars

chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true


php artisan config:clear || true
php artisan cache:clear || true
php artisan optimize:clear || true




# --- Make folders first ---
umask 0002
mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache

# Permissions
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

# --- Aiven CA cert (DON'T crash if missing) ---
if [ -f /etc/secrets/aiven-ca.pem ]; then
  cp /etc/secrets/aiven-ca.pem /var/www/html/storage/aiven-ca.pem
  chmod 644 /var/www/html/storage/aiven-ca.pem
  chown www-data:www-data /var/www/html/storage/aiven-ca.pem || true
fi

# --- Laravel caches (safe) ---
php artisan optimize:clear || true
#php artisan config:cache || true
#php artisan route:cache  || true
#php artisan view:cache   || true

# --- Run migrations (safe-ish) ---
php artisan migrate --force || true

# --- Nginx config from template ---
: "${PORT:=10000}"
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/sites-enabled/default
ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default


# --- Start services ---
php-fpm -D
nginx -t
nginx -g "daemon off;"

