# Sécurité

Aucun secret réel ne doit être commité dans ce dépôt.

Utiliser des variables d’environnement pour les identifiants de base de données et toute autre donnée sensible. Les fichiers `.env` sont ignorés par Git ; seul `.env.example` peut être versionné.

Les exemples doivent utiliser uniquement des utilisateurs fictifs, des domaines réservés comme `example.invalid` et aucune IP, URL ou donnée interne.

Les mots de passe doivent être stockés exclusivement sous forme de hash produit par `password_hash()` et vérifiés avec `password_verify()`.
