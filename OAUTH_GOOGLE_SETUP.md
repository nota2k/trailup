# Configuration OAuth Google

## Étapes pour configurer l'authentification Google OAuth

### 1. Créer un projet Google Cloud Console

1. Allez sur [Google Cloud Console](https://console.cloud.google.com/)
2. Créez un nouveau projet ou sélectionnez un projet existant
3. Activez l'API "Google+ API" ou "Google Identity Services"

### 2. Créer des identifiants OAuth 2.0

1. Allez dans "APIs & Services" > "Credentials"
2. Cliquez sur "Create Credentials" > "OAuth client ID"
3. Sélectionnez "Web application"
4. Configurez les URI de redirection autorisés :
   - Pour le développement local : `http://localhost:8000/connect/google/check`
   - Pour la production : `https://votre-domaine.com/connect/google/check`
5. Copiez le "Client ID" et le "Client Secret"

### 3. Configurer les variables d'environnement

Ajoutez ces lignes dans votre fichier `.env` :

```env
GOOGLE_CLIENT_ID=votre_client_id_google
GOOGLE_CLIENT_SECRET=votre_client_secret_google
```

### 4. Exécuter la migration

```bash
php bin/console doctrine:migrations:migrate
```

### 5. Tester la connexion

1. Allez sur la page de connexion ou d'inscription
2. Cliquez sur le bouton "Continuer avec Google" ou "S'inscrire avec Google"
3. Vous serez redirigé vers Google pour autoriser l'application
4. Après autorisation, vous serez automatiquement connecté et redirigé vers le backoffice

## Notes importantes

- Les utilisateurs créés via OAuth n'ont pas de mot de passe (le champ password est nullable)
- Si un utilisateur existe déjà avec le même email, son compte sera lié à Google
- Le nom d'utilisateur est généré automatiquement à partir du nom Google ou de l'email

