# Guide d'Installation de l'API

## Prérequis
- PHP 8.1+
- MySQL/MariaDB
- Apache/Nginx
- Composer
- Git

## Installation

1. **Cloner le projet**
```bash
git clone https://github.com/Alanarex/M1-MDS-2425-API.git
cd M1-MDS-2425-API
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configuration**
```bash
cp .env.example .env
```
Éditer `.env` avec vos informations :
```
DB_HOST=localhost
DB_NAME=hackr_api
DB_USER=votre_utilisateur
DB_PASS=votre_mot_de_passe
JWT_SECRET=votre_clé_secrète
```
Pour générer une clé JWT sécurisée, vous pouvez utiliser [jwtsecret.com](https://jwtsecret.com/generate).

4. **Créer la base de données**
```sql
CREATE DATABASE hackr_api;
```

5. **Exécuter les migrations**
Visitez `http://votre-domaine/migration`

6. **Identifiants par défaut**
- Utilisateur: admin
- Mot de passe: password

## Routes & Exemples

### Authentification

#### 1. Route - `/login`

**Méthode :** POST

**Paramètres attendus :**
```json
{
    "username": "admin",
    "password": "password"
}
```

**Réponse :**
```json
{
    "success": true,
    "token": "eyJ0eXAiOiJKV1QiLC..."
}
```

### Utilisateurs

#### 2. Route protégée - `/users/new`

**Méthode :** POST

**Paramètres attendus :**
```json
{
    "username": "nouveau_utilisateur",
    "password": "mot_de_passe",
    "is_admin": "0",
    "permissions": ["hackr"]
}
```

**Réponse :**
```json
{
    "success": true,
    "message": "User created successfully"
}
```

#### 3. Route protégée - `/users/edit`

**Méthode :** PUT

**Paramètres attendus :**
```json
{
    "username": "utilisateur",
    "password": "nouveau_mot_de_passe",
    "permissions": ["hackr", "view_users"]
}
```

**Réponse :**
```json
{
    "success": true,
    "message": "User updated successfully"
}
```

#### 4. Route protégée - `/users/delete`

**Méthode :** DELETE

**Paramètres attendus :**
```json
{
    "username": "utilisateur"
}
```

**Réponse :**
```json
{
    "success": true,
    "message": "User and related permissions deleted successfully"
}
```

#### 5. Route protégée - `/users/all`

**Méthode :** GET

**Réponse :**
```json
{
    "success": true,
    "message": "Users retrieved successfully",
    "data": [
        {
            "id": 1,
            "username": "admin",
            "is_admin": 1,
            "permissions": ["admin", "view_users", "edit_users", "delete_users", "create_users", "hackr", "view_permissions", "delete_permissions", "add_permissions"]
        },
        // autres utilisateurs...
    ]
}
```

### Outils Hackr

#### 6. Route protégée - `/email-validation`

**Méthode :** POST

**Paramètres attendus :**
```json
{
    "email": "test@example.com"
}
```

**Réponse :**
```json
{
    "success": true,
    "message": {
        "email": "example@gmail.com",
        "status": "accept_all",
        "result": "risky"
    }
}
```

#### 7. Route protégée - `/check-common-password`

**Méthode :** POST

**Paramètres attendus :**
```json
{
    "password": "123456"
}
```

**Réponse :**
```json
{
    "success": true,
    "message": "Password found",
    "index": 1
}
```

#### 8. Route protégée - `/fetch-subdomains`

**Méthode :** POST

**Paramètres attendus :**
```json
{
    "domain": "google.com"
}
```

**Réponse :**
```json
{
    "success": true,
    "message": "Subdomains found",
    "subdomains": [
        "maps",
        "support",
        "play"
    ]
}
```

#### 9. Route protégée - `/generate-image`

**Méthode :** POST

**Paramètres attendus :**
```json
{
    "first_name": "John",
    "last_name": "Doe"
}
```

**Réponse :**
```json
{
    "success": true,
    "message": "Image generated successfully",
    "user": {
        "photo": "https://randomuser.me/api/portraits/men/79.jpg"
    }
}
```

#### 10. Route protégée - `/generate-identity`

**Méthode :** GET

**Réponse :**
```json
{
    "success": true,
    "message": "Fictitious identity generated successfully",
    "identity": {
        "name": "Ronnie Ryan",
        "gender": "male",
        "email": "ronnie.ryan@example.com",
        "phone": "06-4114-2810",
        "cell": "0418-476-259",
        "address": {
            "street": "6068 W 6th St",
            "city": "Perth",
            "state": "South Australia",
            "country": "Australia",
            "postcode": 9584
        }
    }
}
```

#### 11. Route protégée - `/information-crawler`

**Méthode :** POST

**Paramètres attendus :**
```json
{
    "first_name": "John",
    "last_name": "Doe"
}
```

**Réponse :**
```json
{
    "success": true,
    "message": "Information retrieved successfully",
    "results": [
        {
            "title": "John Doe",
            "url": "https://open.spotify.com/artist/4uyMcYXJM1rO5fvC9lOKwF",
            "snippet": "As one of the founding members of the Los Angeles punk band X, John Doe was one of the most influential figures in American alternative rock during the early ' ...",
            "source": "Spotify",
            "favicon": "https://serpapi.com/searches/67887e588f7c661a455b9e4b/images/1b568f19708ed202d32b86ad82e2b753b902cf05300177c7b96e26307d5bb1f1.png"
        },
        {
            "title": "UNKNOWN INDIVIDUAL - JOHN DOE 40",
            "url": "https://www.fbi.gov/wanted/ecap/unknown-individual---john-doe-40",
            "snippet": "John Doe 40 is described as a White male, likely between the ages of 30 and 40 years old. He appears to be heavyset with dark colored hair. John Doe 40 has a ...",
            "source": "FBI.gov",
            "favicon": "https://serpapi.com/searches/67887e588f7c661a455b9e4b/images/1b568f19708ed202d32b86ad82e2b7537c89dc852534d94a55f3f62c6083886b.png"
        },
        {
            "title": "John Doe (TV Series 2002–2003)",
            "url": "https://www.imdb.com/title/tt0320038/",
            "snippet": "A man who seems to know everything but his own name helps police solve crimes as he searches for his identity.A man who seems to know everything but his own ...",
            "source": "IMDb",
            "favicon": "https://serpapi.com/searches/67887e588f7c661a455b9e4b/images/1b568f19708ed202d32b86ad82e2b753c1ad683f5459f3dc2cc589b3f236ea73.jpeg"
        },
        {
            "title": "John Doe Craft Bar & Kitchen - NYC",
            "url": "https://www.johndoenyc.com/",
            "snippet": "John Doe Craft Bar & Kitchen serves up a creative variety of classic cocktails, and vibrant flavor combinations on original dishes sure to become new favorites!",
            "source": "John Doe Craft Bar & Kitchen",
            "favicon": "https://serpapi.com/searches/67887e588f7c661a455b9e4b/images/1b568f19708ed202d32b86ad82e2b753a4dbc41a05a0ef269b03d6459dc616bc.png"
        },
        {
            "title": "John Doe",
            "url": "https://en.wikipedia.org/wiki/John_Doe",
            "snippet": "John Doe (male) and Jane Doe (female) are multiple-use placeholder names that are used in the British and American legal system and aside generally in the ...",
            "source": "Wikipedia",
            "favicon": "https://serpapi.com/searches/67887e588f7c661a455b9e4b/images/1b568f19708ed202d32b86ad82e2b7539534d01622491eebebadbdcd0b2842db.png"
        }
    ]
}
```

#### 12. Route protégée - `/generate-password`

**Méthode :** POST

**Paramètres attendus :**
```json
{
    "success": true,
    "message": "Password generated successfully",
    "password": "lfN#4Vv>_g0g"
}
```

**Réponse :**
```json
{
    "success": true,
    "message": "Password generated successfully",
    "password": "kji7S^FN2!&s"
}
```

#### 13. Route protégée - `/simulate-ddos`

**Méthode :** POST

**Paramètres attendus :**
```json
{
    "domain": "example.com",
    "packets": 1000,
    "bytes": 512,
    "time": 10
}
```

**Réponse :**
```json
{
    "success": true,
    "message": "Simulation completed"
}
```

#### 14. Route protégée - `/email-spammer`

**Méthode :** POST

**Paramètres attendus :**
```json
{
    "to": "ak5871192@gmail.com",
    "subject": "Hello!",
    "message": "This is a spam test message.",
    "count": 5
}
```

**Réponse :**
```json
{
    "success": true,
    "message": "Emails sent successfully"
}
```

### Gestion des Permissions

#### 15. Route protégée - `/permissions`

**Méthode :** GET

**Réponse :**
```json
{
    "success": true,
    "data": [
        {"id": 1, "name": "admin"},
        {"id": 2, "name": "view_users"},
        // autres permissions...
    ]
}
```

#### 16. Route protégée - `/permissions/new`

**Méthode :** POST

**Paramètres attendus :**
```json
{
    "name": "nouvelle_permission"
}
```

**Réponse :**
```json
{
    "success": true,
    "message": "Permission added successfully"
}
```

#### 17. Route protégée - `/permissions/delete`

**Méthode :** DELETE

**Paramètres attendus :**
```json
{
    "name": "nouvelle_permission"
}
```

**Réponse :**
```json
{
    "success": true,
    "message": "Permission deleted successfully"
}
```

### Logs

#### 18. Route protégée - `/logs/all`

**Méthode :** GET

**Réponse :**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "username": "admin",
            "route": "/login",
            "description": "User logged in",
            "created_at": "2023-10-01T12:34:56Z"
        },
        // autres logs...
    ]
}
```

## Notes de Sécurité
- Changez immédiatement le mot de passe admin par défaut
- Modifiez la clé secrète JWT dans .env
- Activez HTTPS en production
- Vérifiez les permissions des fichiers (755 pour les dossiers, 644 pour les fichiers)

## Dépannage
- Vérifiez que mod_rewrite est activé sur Apache
- Assurez-vous que les extensions PHP PDO et MySQL sont activées
- Consultez les logs PHP en cas d'erreur
- Vérifiez les permissions des fichiers et dossiers

```markdown
## Utilisation de Swagger pour la Documentation de l'API

Pour utiliser Swagger afin de documenter et interagir avec votre API, suivez ces étapes :

---

### 1. **Définir l’hôte dans `swagger.json`**

Ouvrez votre fichier `swagger.json` et configurez la propriété `host` avec l’hôte et le port où votre API est exécutée. Par exemple :

```json
{
  "swagger": "2.0",
  "info": {
    "title": "Documentation de l'API",
    "description": "Documentation de l'API pour les outils de hacking",
    "version": "1.0.0"
  },
  "host": "localhost:8000",
  "basePath": "/",
  "schemes": [
    "http"
  ],
  "paths": {
    // Vos chemins d'API ici
  }
}
```

---

### 2. **Configurer le fichier `swagger-initializer.js`**

Ouvrez le fichier `swagger-ui/dist/swagger-initializer.js` et configurez la propriété `url` pour qu’elle pointe vers votre fichier `swagger.json`. Par exemple :

```javascript
url: "http://localhost:8000/swagger.json",
```

---

### 3. **Démarrer Swagger UI**

1. Accédez au répertoire `dist` :
   ```bash
   cd swagger-ui/dist
   ```

2. Lancez un serveur intégré PHP :
   ```bash
   php -S localhost:8001
   ```

---

### 4. **Accéder à Swagger UI**

Ouvrez votre navigateur et accédez à l’URL suivante :
[http://localhost:8001](http://localhost:8001).

---

### 5. **Gérer les problèmes de CORS**

Si vous rencontrez des problèmes liés à CORS, vérifiez le fichier de configuration de votre site dans les fichiers de configuration de votre site. Assurez-vous qu’il inclut les lignes suivantes :

```apache
Header set Access-Control-Allow-Origin "*"
Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
Header set Access-Control-Allow-Headers "Content-Type, Authorization"
```

Ensuite, activez le module des en-têtes et redémarrez le serveur :

```bash
sudo a2enmod headers
sudo service apache2 restart
```
