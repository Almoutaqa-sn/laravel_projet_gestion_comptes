# Étape 1 : Choisir une image de base PHP compatible avec Laravel
FROM php:8.3-fpm

# Étape 2 : Installer les dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Étape 3 : Installer Composer depuis une image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Étape 4 : Définir le répertoire de travail
WORKDIR /app

# Étape 5 : Copier les fichiers du projet dans le conteneur
COPY . .

# Étape 6 : Installer les dépendances PHP de Laravel
RUN composer install --no-dev --optimize-autoloader

# Étape 7 : Générer la clé d'application
RUN php artisan key:generate

# Étape 8 : Donner les bons droits à Laravel
RUN chmod -R 777 storage bootstrap/cache

# Étape 9 : Exposer le port 8000 (celui que Laravel utilise)
EXPOSE 8000

# Étape 10 : Démarrer le serveur Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000
