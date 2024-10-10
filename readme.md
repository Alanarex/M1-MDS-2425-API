Here is a README in French that includes the steps for installation and testing the routes, as well as other relevant information.

---

# API REST avec JWT - Guide d'Installation et de Test

## Prérequis
- PHP 8.0 ou version supérieure
- Serveur Apache (ou un environnement WAMP pour Windows)
- Postman (ou tout autre outil pour tester les requêtes HTTP)

## Installation

### 1. Cloner le projet
Clonez ou copiez les fichiers de votre projet dans le répertoire de votre serveur web local (par exemple, `C:/wamp64/www/M1-MDS-2425-API/`).

### 2. Installer les dépendances
Utilisez Composer pour installer les dépendances (comme la bibliothèque JWT). Si Composer n'est pas installé, téléchargez-le depuis [getcomposer.org](https://getcomposer.org/).

Dans le répertoire du projet, exécutez la commande suivante pour installer la bibliothèque JWT :

```bash
composer require firebase/php-jwt
```

### 3. Configurer les routes
Les routes suivantes sont définies dans le fichier `index.php` et permettent de gérer les différentes fonctionnalités de l'API.

#### Routes disponibles :
- **`/` (GET)**: Renvoie un message de bienvenue.
- **`/login` (POST)**: Authentification de l'utilisateur avec un nom d'utilisateur et un mot de passe. Retourne un JWT en cas de succès.
- **`/protected` (GET)**: Route protégée qui nécessite un JWT valide pour y accéder.

## Utilisation

### 1. Tester la route de bienvenue

Envoyez une requête `GET` à l'URL suivante dans votre navigateur ou via Postman :

```
http://localhost/M1-MDS-2425-API/
```

Réponse attendue :
```json
{
  "message": "Welcome to the API"
}
```

### 2. Authentification - Route `/login`

#### Requête :

Envoyez une requête `POST` à l'URL suivante pour vous connecter :

```
http://localhost/M1-MDS-2425-API/login
```

#### Corps de la requête (JSON) :
```json
{
  "username": "admin",
  "password": "password"
}
```

#### Réponse attendue (en cas de succès) :
```json
{
  "success": true,
  "token": "<votre-jwt-token>"
}
```

Le `token` est un JWT qui sera utilisé pour accéder aux routes protégées.

### 3. Route protégée - `/protected`

#### Requête :

Une fois le `token` reçu après la connexion, vous pouvez accéder aux routes protégées en ajoutant le JWT dans l'en-tête `Authorization` de la requête.

Envoyez une requête `GET` à l'URL suivante :

```
http://localhost/M1-MDS-2425-API/protected
```

#### En-tête requis :
- **Key**: `Authorization`
- **Value**: `Bearer <votre-jwt-token>`

Remplacez `<votre-jwt-token>` par le token reçu lors de la connexion.

#### Réponse attendue :
Si le JWT est valide, vous recevrez une réponse similaire à celle-ci :
```json
{
  "success": true,
  "message": "Access granted to protected route",
  "user": {
    "user": "admin",
    "exp": 1633201123
  }
}
```

Si le JWT est manquant ou invalide, la réponse sera :
```json
{
  "success": false,
  "message": "Invalid or expired token"
}
```

### 4. Erreurs 404

Si vous accédez à une route non définie, une réponse 404 sera retournée :

```json
{
  "message": "Not Found"
}
```

## Configuration supplémentaire

Si vous utilisez Apache et rencontrez des problèmes avec l'en-tête `Authorization` (JWT), assurez-vous d'ajouter la règle suivante dans votre fichier `.htaccess` :

```apache
RewriteEngine On
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule ^(.*)$ - [E=HTTP_AUTHORIZATION:%1]
```

## Exigences techniques
- **PHP**: Version 8.0 ou supérieure
- **JWT**: La bibliothèque JWT de Firebase est utilisée pour générer et valider les tokens.
- **Postman**: Un outil pour tester les requêtes HTTP. 