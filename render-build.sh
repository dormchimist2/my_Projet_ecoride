#!/usr/bin/env bash

# Exécuter les migrations
php bin/console doctrine:migrations:migrate --no-interaction
