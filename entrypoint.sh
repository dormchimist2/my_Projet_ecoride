#!/bin/sh

# Écriture de la variable DATABASE_URL dans .env.dev
echo "DATABASE_URL=postgresql://ecowhat_user:KsYwBbfZNOV57vwRCgmUOYI26aUN2PfH@dpg-cuqs8n56l47c73chtkn0-a.frankfurt-postgres.render.com/ecowhat" > /var/www/symfony/.env.dev

# Lancer les migrations (optionnel, si nécessaire)
# php bin/console doctrine:migrations:migrate --no-interaction

# Démarrer PHP-FPM et Nginx
php-fpm82 & nginx -g 'daemon off;'
