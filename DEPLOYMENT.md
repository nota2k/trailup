# Guide de déploiement - TrailUp

Ce guide vous explique comment builder et déployer votre application Symfony TrailUp en production.

## 📋 Prérequis

- PHP >= 8.1
- Composer
- Accès SSH au serveur de production
- Base de données MySQL/MariaDB configurée
- Serveur web (Apache/Nginx)

## 🚀 Étapes de déploiement

### 1. Préparer l'environnement local

#### 1.1. Vérifier les fichiers à exclure

Assurez-vous que votre `.gitignore` contient :
```
/.env
/.env.local
/.env.local.php
/.env.*.local
/var/
/vendor/
/public/bundles/
/public/uploads/
```

#### 1.2. Créer le fichier `.env.prod` (optionnel)

Vous pouvez créer un fichier `.env.prod` pour tester la configuration de production localement :

```bash
cp .env .env.prod
```

Puis modifiez les variables d'environnement pour la production.

### 2. Préparer le serveur de production

#### 2.1. Connexion au serveur

```bash
ssh utilisateur@votre-serveur.com
```

#### 2.2. Cloner le dépôt Git

```bash
cd /var/www  # ou votre répertoire de déploiement
git clone https://github.com/votre-repo/trailup.git
cd trailup
```

### 3. Configuration de l'environnement de production

#### 3.1. Créer le fichier `.env` sur le serveur

```bash
cp .env .env.local
nano .env.local  # ou votre éditeur préféré
```

#### 3.2. Configurer les variables d'environnement

Modifiez les variables suivantes dans `.env.local` :

```env
APP_ENV=prod
APP_SECRET=votre-secret-aleatoire-tres-long-et-securise

# Base de données
DATABASE_URL="mysql://user:password@127.0.0.1:3306/trailup_db?serverVersion=8.0&charset=utf8mb4"

# OAuth Google (si utilisé)
GOOGLE_CLIENT_ID=votre-client-id
GOOGLE_CLIENT_SECRET=votre-client-secret

# URL de l'application
APP_URL=https://votre-domaine.com
```

**⚠️ Important :**
- `APP_ENV` doit être `prod`
- `APP_SECRET` doit être unique et sécurisé (générez-le avec `php bin/console secrets:generate-app-secret`)
- Ne commitez JAMAIS le fichier `.env.local`

### 4. Installation des dépendances

#### 4.1. Installer les dépendances Composer (sans dev)

```bash
composer install --no-dev --optimize-autoloader
```

Cette commande :
- Installe uniquement les dépendances de production
- Optimise l'autoloader pour de meilleures performances
- Exclut les outils de développement (Maker Bundle, Profiler, etc.)

#### 4.2. Vérifier les permissions

```bash
# Donner les permissions d'écriture aux dossiers nécessaires
chmod -R 775 var/
chmod -R 775 public/uploads/
chown -R www-data:www-data var/ public/uploads/  # Adaptez selon votre serveur
```

### 5. Configuration de la base de données

#### 5.1. Exécuter les migrations

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

#### 5.2. (Optionnel) Charger les fixtures en production

⚠️ **Attention** : Ne chargez les fixtures que si nécessaire et sur un environnement de test.

```bash
php bin/console doctrine:fixtures:load --no-interaction
```

### 6. Optimisation pour la production

#### 6.1. Vider et réchauffer le cache

```bash
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug
```

#### 6.2. Installer les assets publics

```bash
php bin/console assets:install public --symlink --relative
```

### 7. Configuration du serveur web

#### 7.1. Configuration Apache (.htaccess)

Le fichier `public/.htaccess` devrait déjà être présent. Vérifiez qu'il contient :

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI}::$1 ^(/.+)/(.*)::\2$
    RewriteRule ^(.*) - [E=BASE:%1]
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [QSA,L]
</IfModule>
```

#### 7.2. Configuration Nginx

Si vous utilisez Nginx, créez un fichier de configuration :

```nginx
server {
    listen 80;
    server_name votre-domaine.com;
    root /var/www/trailup/public;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        internal;
    }

    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/trailup_error.log;
    access_log /var/log/nginx/trailup_access.log;
}
```

### 8. Sécurité

#### 8.1. Vérifier les permissions

```bash
# Les dossiers var/ et public/uploads/ doivent être accessibles en écriture
chmod -R 775 var/ public/uploads/

# Les autres fichiers doivent être en lecture seule
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
```

#### 8.2. Désactiver l'affichage des erreurs

Vérifiez que dans `.env.local` :
```env
APP_DEBUG=0
```

#### 8.3. Configurer HTTPS

Assurez-vous d'avoir un certificat SSL valide pour votre domaine.

### 9. Script de déploiement automatisé

Créez un script `deploy.sh` pour automatiser le déploiement :

```bash
#!/bin/bash

set -e

echo "🚀 Déploiement de TrailUp..."

# Aller dans le répertoire du projet
cd /var/www/trailup

# Récupérer les dernières modifications
git pull origin main

# Installer les dépendances
composer install --no-dev --optimize-autoloader

# Exécuter les migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Vider et réchauffer le cache
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug

# Installer les assets
php bin/console assets:install public --symlink --relative

# Ajuster les permissions
chmod -R 775 var/ public/uploads/
chown -R www-data:www-data var/ public/uploads/

echo "✅ Déploiement terminé avec succès !"
```

Rendez-le exécutable :
```bash
chmod +x deploy.sh
```

### 10. Vérifications post-déploiement

#### 10.1. Tester l'application

- Visitez `https://votre-domaine.com`
- Vérifiez que les pages se chargent correctement
- Testez la connexion
- Vérifiez que les assets (CSS, JS, images) se chargent

#### 10.2. Vérifier les logs

```bash
# Logs Symfony
tail -f var/log/prod.log

# Logs du serveur web
tail -f /var/log/nginx/error.log  # Nginx
tail -f /var/log/apache2/error.log  # Apache
```

#### 10.3. Vérifier les performances

- Utilisez des outils comme Google PageSpeed Insights
- Vérifiez que le cache fonctionne correctement
- Surveillez l'utilisation de la mémoire

## 🔄 Mise à jour de l'application

Pour mettre à jour l'application après un déploiement initial :

```bash
# Option 1 : Utiliser le script de déploiement
./deploy.sh

# Option 2 : Commandes manuelles
git pull origin main
composer install --no-dev --optimize-autoloader
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug
```

## 📝 Checklist de déploiement

- [ ] Variables d'environnement configurées (`.env.local`)
- [ ] `APP_ENV=prod` et `APP_DEBUG=0`
- [ ] `APP_SECRET` généré et sécurisé
- [ ] Base de données configurée et migrations exécutées
- [ ] Dépendances installées avec `--no-dev`
- [ ] Cache vidé et réchauffé
- [ ] Permissions des dossiers `var/` et `public/uploads/` correctes
- [ ] Serveur web configuré (Apache/Nginx)
- [ ] HTTPS configuré
- [ ] OAuth Google configuré (si utilisé)
- [ ] Tests fonctionnels effectués

## 🐛 Résolution de problèmes

### Erreur de permissions

```bash
chmod -R 775 var/ public/uploads/
chown -R www-data:www-data var/ public/uploads/
```

### Cache corrompu

```bash
rm -rf var/cache/*
php bin/console cache:warmup --env=prod --no-debug
```

### Erreur de base de données

```bash
# Vérifier la connexion
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
```

### Assets non chargés

```bash
php bin/console assets:install public --symlink --relative
```

## 📚 Ressources supplémentaires

- [Documentation Symfony - Déploiement](https://symfony.com/doc/current/deployment.html)
- [Documentation Symfony - Performance](https://symfony.com/doc/current/performance.html)
- [Documentation Symfony - Sécurité](https://symfony.com/doc/current/security.html)

