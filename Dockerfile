FROM php:8.2-fpm-alpine

# Installer les paquets nécessaires, y compris PostgreSQL et ses dépendances
RUN apk add --no-cache \
    nginx \
    nodejs \
    yarn \
    postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql intl opcache

# Installer Composer depuis l'image officielle Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/symfony

# Copier les fichiers du projet
COPY . .

# Ajuster les permissions pour éviter les erreurs de Render
RUN mkdir -p /var/www/symfony/var /var/www/symfony/node_modules \
    && chown -R www-data:www-data /var/www/symfony

# Passer à l’utilisateur www-data
USER www-data

# Installer Composer et les dépendances
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Vérifier si bin/console existe
RUN ls -l /var/www/symfony/bin/

# Installer Yarn et gérer les assets
RUN yarn install --frozen-lockfile
RUN yarn encore production

# Effacer le cache Symfony
RUN php /var/www/symfony/bin/console cache:clear --env=prod --no-debug

# Copier la configuration Nginx et le script de démarrage
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Exposer le port 80
EXPOSE 80

# Lancer Nginx et PHP-FPM
ENTRYPOINT ["/start.sh"]
