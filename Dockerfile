# Étape de build pour Node.js
FROM node:18 as node-builder
WORKDIR /app

# Copie des fichiers nécessaires pour yarn
COPY package.json ./
COPY assets ./assets/
COPY webpack.config.js ./

# Installation des dépendances Node.js
RUN yarn install \
    && yarn add @hotwired/stimulus \
    && yarn add @symfony/webpack-encore \
    && yarn add sass \
    && yarn add sass-loader \
    && yarn add webpack-notifier \
    && yarn encore production

# Image PHP principale
FROM php:8.2-fpm

# Configuration de Composer pour permettre l'exécution en tant que root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    libpq-dev \
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

# Définition du répertoire de travail
WORKDIR /var/www/symfony

# Copie des fichiers de configuration Composer
COPY composer.json composer.lock ./

# Création et configuration des répertoires nécessaires
RUN mkdir -p var/cache var/log public/build \
    && chmod -R 777 var public/build

# Installation des dépendances PHP sans scripts
RUN composer install \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Copie du reste des fichiers du projet
COPY . .

# Copie des assets buildés depuis l'étape node-builder
COPY --from=node-builder /app/public/build ./public/build

# Génération des fichiers de cache
RUN php bin/console cache:clear --env=prod --no-warmup \
    && php bin/console cache:warmup --env=prod \
    && chown -R www-data:www-data var public/build

# Exposition du port PHP-FPM
EXPOSE 9000

# Définition des variables d'environnement pour la production
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Commande par défaut
CMD ["php-fpm"]