#!/bin/bash
# ============================================================
# Script de instalare dupa upload pe Hostico
# Ruleaza via SSH din folderul: ~/meseriasionline.ro/
# ============================================================

echo "=== 1. Instalare dependinte PHP (Composer) ==="
composer install --no-dev --optimize-autoloader

echo ""
echo "=== 2. Instalare dependinte Node.js si build assets ==="
# Daca Node.js este disponibil pe server:
# npm install && npm run build
# 
# DACA NODE.JS NU E DISPONIBIL:
# Ruleaza local pe PC: npm install && npm run build
# Apoi incarca folderul public/build/ pe server

echo ""
echo "=== 3. Copiaza .env.production in .env ==="
cp .env.production .env

echo ""
echo "=== 4. Genereaza APP_KEY ==="
php artisan key:generate

echo ""
echo "=== 5. Ruleaza migrarile ==="
php artisan migrate --force

echo ""
echo "=== 6. Creeaza symlink pentru storage ==="
php artisan storage:link

echo ""
echo "=== 7. Genereaza cheile VAPID pentru WebPush ==="
php artisan webpush:vapid

echo ""
echo "=== 8. Cache pentru productie ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo ""
echo "=== 9. Permisiuni foldere ==="
chmod -R 755 storage bootstrap/cache
chmod -R 644 storage/logs

echo ""
echo "=== INSTALARE COMPLETA! ==="
echo "Nu uita sa completezi .env cu datele corecte (DB, mail, reCAPTCHA, VAPID)"
