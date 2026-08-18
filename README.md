# IamRoot Core

Application PHP/MySQL de **centralisation de l’authentification et de la gestion des accès par application**.

> Ce dépôt est une **édition publique reconstruite et assainie** à partir de l’architecture et des comportements fonctionnels de mon projet personnel IamRoot. Il ne prétend pas être une copie bit à bit des anciennes versions locales.

## Objectif

Éviter que chaque application gère séparément ses utilisateurs et ses règles d’accès.

```text
Utilisateur
    ↓
Application cliente
    ↓ HTTP / JSON
IamRoot
    ├── utilisateur ?
    ├── mot de passe valide ?
    ├── utilisateur actif ?
    ├── application active ?
    ├── accès utilisateur ↔ application ?
    ├── rôle ?
    └── permissions ?
    ↓
Réponse JSON
```

Le modèle distingue explicitement **authentification**, **autorisation** et **permissions**.

## Modèle de données

```text
usuarios
    │
    └── usuario_aplicaciones ── aplicaciones
                  │
                  └── roles ── rol_permisos ── permisos
```

Un même utilisateur peut avoir un rôle différent selon l’application.

## Technologies

`PHP 8+` · `PDO` · `MySQL / MariaDB` · `SQL` · `HTTP` · `JSON`

Principes :
- `password_hash()` / `password_verify()` ;
- requêtes préparées PDO ;
- secrets hors du dépôt via variables d’environnement ;
- contrôle utilisateur/application/association actifs ;
- séparation authentification / autorisation ;
- aucune configuration de production publiée.

## Installation locale

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p iamroot_public < database/seed.example.sql
```

Configurer les variables d’environnement décrites dans `.env.example`, puis :

```bash
php scripts/hash_password.php 'mot-de-passe-de-test'
php -S 127.0.0.1:8080 -t public
```

## API

### `POST /api/authenticate.php`

```json
{
  "username": "demo",
  "password": "mot-de-passe",
  "application_id": 1
}
```

Réponse autorisée :

```json
{
  "success": true,
  "code": "LOGIN_OK",
  "user": {
    "id": 1,
    "username": "demo",
    "email": "demo@example.invalid",
    "role": "editor"
  },
  "application": {
    "id": 1,
    "code": "demo-app",
    "name": "Application de démonstration"
  },
  "permissions": ["app.read", "app.write"]
}
```

Les erreurs d’authentification ne révèlent pas si le nom d’utilisateur, le mot de passe ou l’association à l’application est incorrect.

## Sécurité

Cette édition publique ne contient volontairement aucun mot de passe réel, utilisateur de production, IP privée, domaine interne, jeton/API key ou configuration LAB/PPR/PRO réelle.

Voir `SECURITY.md`.

## Portée

IamRoot n’est pas présenté comme une plateforme IAM d’entreprise ni comme un remplacement d’OpenID Connect, OAuth 2.0, LDAP ou Active Directory.

C’est un projet personnel destiné à mettre en pratique :

**authentification · autorisation · rôles · permissions · SQL · intégration HTTP/JSON · PHP/MySQL**

## Portfolio

https://fagobo.com/gonzy/career/projects/iamroot/

---

**Sigfrido Gonzalez Puga · Gonzy**
