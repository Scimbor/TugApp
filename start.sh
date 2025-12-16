#!/bin/bash
set -e

echo "Generating SSL certificates..."
openssl genpkey -algorithm RSA -out /etc/ssl/private/nginx-selfsigned.key 2>/dev/null
openssl req -new -key /etc/ssl/private/nginx-selfsigned.key -out /etc/ssl/private/nginx-selfsigned.csr \
    -subj "/C=PL/ST=PL/O=Laravel/CN=localhost" 2>/dev/null
openssl x509 -req -days 365 -in /etc/ssl/private/nginx-selfsigned.csr \
    -signkey /etc/ssl/private/nginx-selfsigned.key -out /etc/ssl/certs/nginx-selfsigned.crt 2>/dev/null

cd /var/www/html

echo "Installing PHP dependencies (if needed)..."
if [ ! -d "vendor" ]; then
    composer install --no-interaction --optimize-autoloader
fi

echo "Installing Node dependencies (if needed)..."
if [ ! -d "node_modules" ]; then
    npm install
fi

echo "Building frontend assets..."
npm run build

echo "Setting up Laravel..."
mkdir -p storage/{app/public,framework/{cache/data,sessions,testing,views},logs,database} bootstrap/cache packages

[ ! -f "storage/database/database.sqlite" ] && touch storage/database/database.sqlite

chown -R www-data:www-data storage bootstrap/cache packages
chmod -R 775 storage bootstrap/cache packages
[ -f "storage/database/database.sqlite" ] && chmod 666 storage/database/database.sqlite

if [ ! -f ".env" ]; then
    cp .env.example .env
fi

php artisan migrate --force

echo "Starting services..."
/usr/sbin/service php8.4-fpm start
/usr/sbin/service nginx start

echo "Laravel is ready!"
tail -f /dev/null