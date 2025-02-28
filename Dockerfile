FROM php:8.2-fpm-alpine

# Installer les paquets nécessaires
RUN apk add --no-cache \
    nginx \
    nodejs \
    yarn \
    postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql intl opcache

# ✅ Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/symfony

# Copier seulement les fichiers nécessaires AVANT d'installer les dépendances
COPY composer.json composer.lock symfony.lock ./

# ✅ Vérifier que Composer est bien installé
RUN composer --version

# Installer les dépendances PHP
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Maintenant on copie le reste du projet
COPY . .

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
