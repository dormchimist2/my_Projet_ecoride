FROM php:8.2-fpm-alpine

# Installer les paquets nécessaires, y compris PostgreSQL, Git, et ses dépendances
RUN apk add --no-cache \
    nginx \
    nodejs \
    yarn \
    postgresql-dev \
    git \
    && docker-php-ext-install pdo pdo_pgsql intl opcache

# Créer un utilisateur non-root pour des raisons de sécurité
RUN addgroup -g 1000 symfony && adduser -G symfony -u 1000 -D symfony

# Définir le répertoire de travail
WORKDIR /var/www/symfony

# Copier composer depuis l’image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier uniquement les fichiers nécessaires avant d'installer les dépendances
COPY composer.json composer.lock symfony.lock ./

# Passer à l'utilisateur non-root pour installer les dépendances avec Composer
USER symfony

# Installer les dépendances avec Composer
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Revenir à www-data pour exécuter l’application
USER www-data

# Copier le reste du projet
COPY . .

# Gérer les permissions pour les dossiers var/cache et var/logs
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
