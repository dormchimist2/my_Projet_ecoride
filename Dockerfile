FROM php:8.2-fpm-alpine

# Installer les paquets nécessaires
RUN apk add --no-cache nginx nodejs yarn \
    && docker-php-ext-install pdo pdo_pgsql intl opcache

# Installer Composer
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

# Installer les dépendances et construire les assets
RUN composer install --no-interaction --no-dev --optimize-autoloader \
    && yarn install --frozen-lockfile \
    && yarn encore production \
    && php bin/console cache:clear --env=prod --no-debug

# Copier la configuration Nginx et le script de démarrage
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Exposer le port 80
EXPOSE 80

# Lancer Nginx et PHP-FPM
ENTRYPOINT ["/start.sh"]
