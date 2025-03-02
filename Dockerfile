# Utiliser l'image officielle PHP avec FPM
FROM php:8.2-fpm

# Installer les dépendances système nécessaires pour Symfony et PostgreSQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    unzip \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer (gestionnaire de dépendances PHP)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier tout le projet Symfony dans le conteneur
COPY . .

# Donner les bonnes permissions pour l'utilisateur www-data (utilisateur par défaut pour PHP)
RUN chown -R www-data:www-data /var/www/html

# Installer les dépendances PHP sans interagir, en optimisant l'autoload
RUN su www-data -s /bin/sh -c 'composer install --no-interaction --optimize-autoloader'

# Si tu as des commandes spécifiques à Symfony, comme le nettoyage du cache, les exécuter en tant que www-data
RUN su www-data -s /bin/sh -c 'php bin/console cache:clear --env=prod'

# Exposer le port 9000 pour PHP-FPM
EXPOSE 9000

# Démarrer PHP-FPM
CMD ["php-fpm"]
