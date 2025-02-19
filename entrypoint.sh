#!/bin/sh

# Écriture de la variable DATABASE_URL dans .env.dev
echo "DATABASE_URL=${DATABASE_URL}" > /var/www/symfony/.env.dev

# Lancer les migrations (optionnel, si nécessaire)
php bin/console doctrine:migrations:migrate --no-interaction

# Démarrer PHP-FPM et Nginx
php-fpm82 & nginx -g 'daemon off;'
