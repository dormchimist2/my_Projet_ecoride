FROM php:8.2-fpm-alpine

# Installer les paquets nécessaires
RUN apk add --no-cache \
    nginx \
    nodejs \
    yarn \
    postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql intl opcache

# Définir le répertoire de travail
WORKDIR /var/www/symfony

# Installer Composer depuis l’image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#  Ajouter un utilisateur non-root pour exécuter Composer
RUN addgroup -g 1000 symfony && adduser -G symfony -u 1000 -D symfony
USER symfony

# Copier seulement les fichiers nécessaires avant d’installer les dépendances
COPY composer.json composer.lock symfony.lock ./

# Installer les dépendances PHP
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Revenir à l’utilisateur www-data pour exécuter l’application
USER www-data

# Copier le reste du projet
COPY . .

# Gérer les permissions pour le cache et les logs
RUN mkdir -p var/cache var/logs && chmod -R 777 var/cache var/logs

# Installer Yarn et gérer les assets
RUN yarn install --frozen-lockfile
RUN yarn encore production

# Effacer le cache Symfony
RUN php bin/console cache:clear --env=prod --no-debug || true

# Copier la configuration Nginx et le script de démarrage
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Exposer le port 80
EXPOSE 80

# Lancer Nginx et PHP-FPM
ENTRYPOINT ["/start.sh"]
