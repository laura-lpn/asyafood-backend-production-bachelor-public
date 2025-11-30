# Asya Food – API Symfony

<img src="public/img/logo.svg" width="100">

Cette API Symfony constitue la *première version* du backend du projet **Asya Food**, un site de recettes asiatiques.  
Elle expose l’ensemble des endpoints nécessaires au fonctionnement du **front Vue 3** : gestion des recettes, ingrédients, pays, utilisateurs, liste de courses et authentification via **JWT**.

[Frontend associé](https://github.com/laura-lpn/asyafood-frontend-vue)

---

## Authentification

L’API utilise **JWT (JSON Web Token)** pour sécuriser l’accès aux routes privées :

- Connexion → génération d’un token JWT  
- Token envoyé dans l’en-tête `Authorization: Bearer <token>`  
- Middleware de vérification sur toutes les routes protégées  
- Refresh token (si implémenté selon version)

---

## Fonctionnalités principales de l’API

### Utilisateurs
- Inscription  
- Connexion (JWT)  
- Récupération des informations du profil  
- Accès restreint aux données personnelles

### Recettes
- Listing complet  
- Filtrage par : **pays**, **type**, **genre**  
- Affichage détaillé : ingrédients, étapes, temps  

### Ingrédients
- Listing et gestion complète depuis le back-office

### Liste de courses
- Ajout depuis une recette  
- Cumul automatique des ingrédients  
- Suppression, reset, modification  
- Associations user → liste via token JWT

### Administration
(Accès réservé via rôle `ROLE_ADMIN`)

- CRUD recettes  
- CRUD ingrédients  
- CRUD pays / types  
- Gestion des utilisateurs  

---

## Technologies

- Symfony 6.3
- Doctrine ORM
- JWT (lexik/jwt-authentication-bundle)
- MySQL / MariaDB
- Docker + Caddy
- Tailwind CSS

---
## 👩‍💻 Auteure

**Laura Lepannetier**  
Projet réalisé dans le cadre du Bachelor Développement Web.

[GitHub](https://github.com/laura-lpn)
