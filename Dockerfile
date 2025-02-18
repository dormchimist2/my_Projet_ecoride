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
    pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Création d'un utilisateur non-root
RUN groupadd -g ${GROUP_ID} appuser \
    && useradd -u ${USER_ID} -g appuser -s /bin/bash -m appuser

# Configuration de PHP
RUN echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/docker-php-memory-limit.ini

# Installation des dépendances Node.js pour Webpack Encore
RUN npm install -g yarn

# Définition du répertoire de travail
WORKDIR /var/www/symfony

# Copie des fichiers de configuration Composer d'abord
COPY --chown=appuser:appuser composer.json composer.lock ./

# Installation des dépendances PHP en tant que root pour éviter les problèmes de permissions
RUN composer install --no-interaction --no-scripts --no-autoloader

# Copie du reste des fichiers du projet
COPY --chown=appuser:appuser . .

# Finalisation de l'installation Composer
RUN composer dump-autoload --optimize \
    && composer run-script post-install-cmd

# Changement vers l'utilisateur non-root
USER appuser

# Installation et build des assets
RUN yarn install \
    && yarn encore dev

# Exposition du port PHP-FPM
EXPOSE 9000

# Commande par défaut
CMD ["php-fpm"]