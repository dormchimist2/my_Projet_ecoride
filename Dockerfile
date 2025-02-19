FROM nginx:alpine

# Installation de PHP et des extensions nécessaires
RUN apk add --no-cache \
    php82 \
    php82-fpm \
    php82-pdo_pgsql \
    php82-iconv \
    php82-intl \
    php82-zip \
    php82-ctype \
    php82-tokenizer \
    php82-session \
    php82-phar \
    php82-mbstring \
    php82-openssl \
    php82-xml \
    php82-simplexml \
    php82-dom \
    nodejs \
    npm \
    yarn

# Création des liens symboliques pour PHP
RUN ln -s /usr/bin/php82 /usr/bin/php

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/symfony

# Copie des fichiers du projet
COPY . .

ENV DATABASE_URL="postgresql://ecowhat_user:KsYwBbfZNOV57vwRCgmUOYI26aUN2PfH@dpg-cuqs8n56l47c73chtkn0-a.frankfurt-postgres.render.com/ecowhat?sslmode=require"

# Installation des dépendances et build en mode production
RUN composer install --optimize-autoloader \
    && composer dump-autoload --optimize \
    && yarn install \
    && yarn encore production \
    && php bin/console cache:clear --env=dev --no-debug

# Configuration Nginx
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Exposition du port 80
EXPOSE 80

# Avant de démarrer les services
CMD export DATABASE_URL="postgresql://ecowhat_user:KsYwBbfZNOV57vwRCgmUOYI26aUN2PfH@dpg-cuqs8n56l47c73chtkn0-a.frankfurt-postgres.render.com/ecowhat" && \
    php-fpm82 && nginx -g 'daemon off;'