FROM nginx:alpine

# Installation de PHP et des extensions nécessaires
RUN apk add --no-cache \
    php82 php82-fpm php82-pdo_pgsql php82-iconv php82-intl php82-zip \
    php82-ctype php82-tokenizer php82-session php82-phar php82-mbstring \
    php82-openssl php82-xml php82-simplexml php82-dom nodejs yarn \
    && ln -s /usr/bin/php82 /usr/bin/php \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version \
    && yarn --version \
    && rm -rf /var/cache/apk/* /tmp/*

WORKDIR /var/www/symfony

# Copie des fichiers du projet
COPY . .

# Créer un groupe et un utilisateur www-data
RUN addgroup -g 82 -S www-data && adduser -u 82 -S -G www-data www-data

# Donner les bonnes permissions
RUN chown -R www-data:www-data /var/www/symfony \
    && chown -R www-data:www-data /var/www/symfony/var \
    && chown -R www-data:www-data /var/www/symfony/node_modules \
    && chmod -R 755 /var/www/symfony


# Installation des dépendances et build en mode production
RUN composer install --optimize-autoloader \
    && composer dump-autoload --optimize \
    && yarn install \
    && yarn encore production \
    && php bin/console cache:clear --env=prod --no-debug

# Configuration Nginx
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Copier explicitement entrypoint.sh à la racine
COPY entrypoint.sh /entrypoint.sh

# Exposition du port 80
EXPOSE 80

# Donner les bonnes permissions au script
RUN chmod +x /entrypoint.sh

# Démarrage du point d'entrée
ENTRYPOINT ["/entrypoint.sh"]
