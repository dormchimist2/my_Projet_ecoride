#!/usr/bin/env bash

echo "=== Vérification de la version PHP ==="
php -v

echo "=== Vérification des extensions PHP ==="
php -m

echo "=== Vérification de la connexion à la base de données ==="
php -r "try { new PDO(getenv('DATABASE_URL')); echo 'Connexion réussie !'; } catch (Exception \$e) { echo 'Erreur: ' . \$e->getMessage(); }"

# Générer les migrations
echo "=== Génération des migrations ==="
php bin/console doctrine:migrations:diff --no-interaction

# Appliquer les migrations
echo "=== Application des migrations ==="
php bin/console doctrine:migrations:migrate --no-interaction

echo "=== Migrations appliquées avec succès ==="
