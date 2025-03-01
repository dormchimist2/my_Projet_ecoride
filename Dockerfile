# Utilisation de l'image officielle PHP 8.2 avec Apache
FROM php:8.2-apache

# Définition du dossier de travail
WORKDIR /var/www/html

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql opcache

# Activation des modules Apache
RUN a2enmod rewrite

# Installer Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installation de Node.js et Yarn pour Webpack Encore
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install --global yarn

# Copie des fichiers du projet
COPY . /var/www/html

# Configuration des permissions (pour éviter les erreurs avec Symfony)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Exécuter les commandes Symfony en tant que www-data
RUN su www-data -s /bin/sh -c 'composer require symfony/runtime'

# Installation des dépendances PHP
RUN su www-data -s /bin/sh -c 'composer install --no-interaction --optimize-autoloader --no-scripts'

# Générer l'autoload optimisé pour Composer
RUN su www-data -s /bin/sh -c 'composer dump-autoload --optimize'

# Exécuter les commandes Symfony en tant que www-data
RUN su www-data -s /bin/sh -c 'php bin/console cache:clear --no-warmup'
RUN su www-data -s /bin/sh -c 'php bin/console assets:install %PUBLIC_DIR%'

# Exécuter les scripts post-installation de Composer
RUN su www-data -s /bin/sh -c 'composer run-script auto-scripts'

# Installation des dépendances Node.js et compilation Webpack Encore
RUN su www-data -s /bin/sh -c 'yarn install && yarn encore production'

# Exécuter les migrations Doctrine au démarrage (si nécessaire)
# Pour cette étape, tu peux définir un script qui exécutera les migrations en toute sécurité
#ENTRYPOINT ["docker/entrypoint.sh"]

# Démarrage d'Apache
CMD ["apache2-foreground"]
