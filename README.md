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
composer install
```

## Utilisation

## Configuration supplémentaire

Si vous utilisez Apache et rencontrez des problèmes avec l'en-tête `Authorization` (JWT), assurez-vous d'ajouter la règle suivante dans votre fichier `.htaccess` :

```apache
RewriteEngine On
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule ^(.*)$ - [E=HTTP_AUTHORIZATION:%1]
```