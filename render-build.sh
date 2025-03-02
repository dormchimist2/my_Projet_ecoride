#!/usr/bin/env bash

# Générer les migrations si nécessaire
php bin/console doctrine:migrations:diff --no-interaction

# Exécuter les migrations
php bin/console doctrine:migrations:migrate --no-interaction
