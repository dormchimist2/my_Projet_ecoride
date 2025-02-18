# Utilisation de l'image PHP 8.2 FPM officielle
FROM php:8.2-fpm

# Arguments pour la configuration
ARG USER_ID=1000
ARG GROUP_ID=1000

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    zip \
    nodejs \
    npm \
    && docker-php-ext-install \
    intl \
    zip \
    pdo_mysql \
    && docker-php-ext-enable \
    intl \
    zip \
    pdo_mysql

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Création d'un utilisateur non-root
RUN groupadd -g ${GROUP_ID} appuser \
    && useradd -u ${USER_ID} -g appuser -s /bin/bash -m appuser

# Configuration de PHP
COPY php.ini-development /usr/local/etc/php/php.ini
RUN sed -i 's/memory_limit = 128M/memory_limit = 512M/g' /usr/local/etc/php/php.ini

# Installation des dépendances Node.js pour Webpack Encore
RUN npm install -g yarn

# Définition du répertoire de travail
WORKDIR /var/www/symfony

# Changement vers l'utilisateur non-root
USER appuser

# Copie des fichiers du projet
COPY --chown=appuser:appuser . .

# Installation des dépendances PHP
RUN composer install --no-interaction

# Installation des dépendances Node.js
RUN yarn install
RUN yarn encore dev

# Exposition du port PHP-FPM
EXPOSE 9000

# Commande par défaut
CMD ["php-fpm"]