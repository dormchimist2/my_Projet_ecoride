# Utilisation de l'image PHP 8.2 FPM officielle
FROM php:8.2-fpm

# Arguments pour la configuration
ARG USER_ID=1000
ARG GROUP_ID=1000

# Configuration de Composer pour permettre l'exécution en tant que root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    libpq-dev \
    zip \
    nodejs \
    npm \
    && docker-php-ext-install \
    intl \
    zip \
    pdo_pgsql \
    && docker-php-ext-enable \
    intl \
    zip \
    pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration de PHP
RUN echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/docker-php-memory-limit.ini

# Installation des dépendances Node.js pour Webpack Encore
RUN npm install -g yarn

# Définition du répertoire de travail
WORKDIR /var/www/symfony

# Copie des fichiers de configuration Composer d'abord
COPY composer.json composer.lock ./

# Création et configuration des répertoires nécessaires
RUN mkdir -p var/cache var/log public/build \
    && chmod -R 777 var public/build

# Installation des dépendances PHP sans scripts
RUN composer install \
    --no-scripts \
    --no-interaction \
    --prefer-dist

# Copie du reste des fichiers du projet
COPY . .

# Génération des fichiers de cache et assets
RUN composer dump-autoload --optimize \
    && php bin/console cache:clear --env=prod --no-warmup \
    && php bin/console cache:warmup --env=prod \
    && yarn install \
    && yarn encore production

# Configuration des permissions finales
RUN chown -R www-data:www-data var public/build

# Exposition du port PHP-FPM
EXPOSE 9000

# Définition des variables d'environnement pour la production
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Commande par défaut
CMD ["php-fpm"]