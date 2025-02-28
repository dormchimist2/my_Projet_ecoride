FROM php:8.2-fpm-alpine

#  Mettre à jour le cache Alpine et installer les dépendances nécessaires
RUN apk update && apk add --no-cache \
    nginx \
    nodejs \
    yarn \
    postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql intl opcache

#  Définir le bon répertoire de travail
WORKDIR /var/www/symfony

#  Copier seulement les fichiers nécessaires AVANT d'installer les dépendances
COPY composer.json composer.lock symfony.lock ./

#  Installer Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Vérifier que `composer.json` est bien copié
RUN ls -l /var/www/symfony/composer.json

#  Installer les dépendances PHP
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Maintenant, on copie le reste du projet
COPY . .

#  Assurer que le cache et les logs sont bien gérés
RUN mkdir -p var/cache var/logs && chmod -R 777 var/cache var/logs

#  Vérifier si `bin/console` existe
RUN ls -l /var/www/symfony/bin/

#  Installer Yarn et gérer les assets (en root pour éviter les permissions)
RUN yarn install --frozen-lockfile
RUN yarn encore production

#  Effacer le cache Symfony
RUN php bin/console cache:clear --env=prod --no-debug

#  Copier la configuration Nginx et le script de démarrage
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY start.sh /start.sh
RUN chmod +x /start.sh

#  Exposer le port 80
EXPOSE 80

#  Lancer Nginx et PHP-FPM
ENTRYPOINT ["/start.sh"]
