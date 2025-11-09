# TrailUp - Guide de démarrage

Ce projet est une application Symfony 6.3 pour la gestion d'itinéraires de randonnée équestre.

## Prérequis

- **PHP** >= 8.1 avec les extensions suivantes :
  - `ext-ctype`
  - `ext-iconv`
- **Composer** (gestionnaire de dépendances PHP)
- **Base de données** (MySQL, PostgreSQL, SQLite, etc.)
- **Symfony CLI** (optionnel, mais recommandé)

## Installation

### 1. Installer les dépendances

```bash
composer install
```

### 2. Configurer l'environnement

Le fichier `.env` contient les variables d'environnement. Assurez-vous de configurer au minimum :

- **DATABASE_URL** : URL de connexion à votre base de données
  - Exemple MySQL : `DATABASE_URL="mysql://user:password@127.0.0.1:3306/trailup?serverVersion=8.0"`
  - Exemple PostgreSQL : `DATABASE_URL="postgresql://user:password@127.0.0.1:5432/trailup?serverVersion=15"`
  - Exemple SQLite : `DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"`

- **APP_ENV** : Environnement d'exécution (`dev`, `prod`, `test`)
- **APP_SECRET** : Clé secrète pour la sécurité (générée automatiquement si absente)

Vous pouvez créer un fichier `.env.local` pour surcharger les valeurs du `.env` sans modifier le fichier versionné.

### 3. Créer la base de données

```bash
php bin/console doctrine:database:create
```

### 4. Exécuter les migrations

```bash
php bin/console doctrine:migrations:migrate
```

### 5. Charger les données de test (optionnel)

```bash
php bin/console doctrine:fixtures:load
```

## Lancer le serveur de développement

### Option 1 : Utiliser Symfony CLI (recommandé)

```bash
symfony server:start
```

Le serveur sera accessible sur `http://localhost:8000` (ou un autre port si 8000 est occupé).

### Option 2 : Utiliser le serveur PHP intégré

```bash
php -S localhost:8000 -t public
```

### Option 3 : Utiliser un serveur web (Apache/Nginx)

Configurez votre serveur web pour pointer vers le dossier `public/`.

## Commandes utiles

### Vider le cache

```bash
php bin/console cache:clear
```

### Créer une nouvelle migration

```bash
php bin/console make:migration
```

### Voir les routes disponibles

```bash
php bin/console debug:router
```

### Voir la configuration

```bash
php bin/console debug:container
```

## Structure du projet

- `config/` : Configuration de l'application
- `migrations/` : Migrations de base de données Doctrine
- `public/` : Point d'entrée web (dossier public)
- `src/` : Code source de l'application
  - `Controller/` : Contrôleurs
  - `Entity/` : Entités Doctrine
  - `Form/` : Formulaires Symfony
  - `Repository/` : Repositories Doctrine
  - `Security/` : Configuration de sécurité
- `templates/` : Templates Twig
- `var/` : Fichiers générés (cache, logs)

## Environnements

- **dev** : Environnement de développement (avec profiler web)
- **prod** : Environnement de production
- **test** : Environnement de test

Pour changer d'environnement, modifiez la variable `APP_ENV` dans votre fichier `.env` ou `.env.local`.

## Dépannage

### Avertissements de dépréciation PHP

Si vous voyez des avertissements comme `Deprecated: Constant E_STRICT is deprecated`, c'est **normal et non bloquant**. Ces avertissements proviennent de Symfony 6.3 utilisé avec PHP 8.3+.

**Solutions :**

1. **Les ignorer** (recommandé) : Ces avertissements n'affectent pas le fonctionnement de l'application. Ils disparaîtront lorsque Symfony sera mis à jour pour être compatible avec PHP 8.3+.

2. **Réduire les avertissements** : 
   - Un fichier `.user.ini` a été créé à la racine du projet pour réduire les avertissements dans les requêtes web
   - Les fichiers `bin/console` et `public/index.php` ont été modifiés pour réduire les avertissements dans les commandes console et les requêtes web
   - Note : Les avertissements provenant du code Symfony dans `vendor/` ne peuvent pas être supprimés sans mettre à jour Symfony

3. **Mettre à jour Symfony** (solution à long terme) : Mettre à jour vers Symfony 6.4+ ou 7.x qui sont compatibles avec PHP 8.3+ :
   ```bash
   composer require symfony/framework-bundle:"^6.4" --no-interaction
   ```
   ⚠️ **Attention** : Cela peut nécessiter d'autres modifications dans votre code.

### Erreur de permissions

Si vous rencontrez des erreurs de permissions sur le dossier `var/` :

```bash
chmod -R 777 var/
```

### Erreur de connexion à la base de données

Vérifiez que :
1. Votre base de données est démarrée
2. Les identifiants dans `DATABASE_URL` sont corrects
3. La base de données existe (ou utilisez `doctrine:database:create`)

### Erreur "APP_SECRET not set"

Générez une clé secrète :

```bash
php bin/console secrets:generate-keys
```

Ou ajoutez manuellement dans votre `.env` :

```
APP_SECRET=votre_cle_secrete_aleatoire_ici
```

## Support

Pour plus d'informations sur Symfony, consultez la [documentation officielle](https://symfony.com/doc/6.3/index.html).

