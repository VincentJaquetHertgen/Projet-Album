# Deefy — Mini-projet BUT Informatique S3 (2026)

Binôme : Vincent Jaquet-Hertgen et _(nom du binôme)_

URL de test : https://webetu.iutnc.univ-lorraine.fr/www/_(login)_/_(dossier)_/index.php

## Tableau de bord des fonctionnalités

| N° | Fonctionnalité | Action (`?action=`) | Autorisation | État |
|----|----------------|---------------------|--------------|------|
| 1 | Mes playlists (liste des playlists en BD) | `my-playlists` | Utilisateur authentifié | ✅ |
| 2 | Afficher une playlist (devient la playlist courante en session) | `playlist&id=N` | Authentifié + propriétaire | ✅ |
| 3 | Ajouter une piste à la playlist courante (formulaire + upload mp3) | `add-track` | Authentifié + propriétaire | ✅ |
| 4 | Créer une playlist vide (stockée en BD, devient la courante) | `add-playlist` | Utilisateur authentifié | ✅ |
| 5 | Afficher la playlist courante | `playlist` | Authentifié + propriétaire | ✅ |
| 6 | S'inscrire (rôle STANDARD) | `add-user` | Public | ✅ |
| 7 | S'authentifier | `signin` | Public | ✅ |
| + | Se déconnecter | `signout` | — | ✅ |

## Sécurité

- Mots de passe hachés avec `password_hash` (bcrypt, cost 12), vérifiés avec `password_verify` ; 10 caractères minimum.
- Injection SQL : uniquement des requêtes préparées PDO.
- XSS : tout affichage passe par `Html::e()` (`htmlspecialchars`) ; les données sont stockées brutes.
- Autorisations vérifiées côté serveur (`Authz`) : propriétaire de la playlist via la table `user2playlist`.
- Session : `session_regenerate_id` à la connexion/déconnexion.
- Upload : extension `.mp3` + type MIME lu sur le contenu (`finfo`), nom de fichier aléatoire imposé par le serveur.
- `db.config.ini` et les `.sql` sont bloqués par `.htaccess`.

## Installation

1. Créer la base et exécuter `sql/deefy.sql` puis `sql/deefy_data.sql` (données de test).
2. Copier `db.config.ini.example` en `db.config.ini` et renseigner les identifiants de la BD.
3. S'assurer que le dossier `audio/` est accessible en écriture par PHP.
4. Ouvrir `index.php`.

## Comptes de test

| Email | Mot de passe | Playlist |
|-------|--------------|----------|
| user1@deefy.fr | testtest12 | « Playlist de user1 » (id 1) |
| user2@deefy.fr | testtest34 | « Playlist de user2 » (id 2) |

Pour tester les autorisations : connecté en user1, `?action=playlist&id=2` doit renvoyer « Accès refusé ».
