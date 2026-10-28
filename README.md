# Juliecreations – Site e-commerce (Symfony)

Site e-commerce développé pour ma micro-entreprise **juliecreations** (produits faits main et personnalisés), dans le cadre de mon titre professionnel Concepteur Développeur d'Applications (2025-2026).

Il fait partie d'une solution web complète : il fournit les données (articles, stocks, profils) à l'[application Java](https://github.com/juliebreg2603-blip/juliecreationsappli) par une API.

## Technologies
- PHP, Symfony, Twig
- HTML, CSS, Bootstrap, JavaScript
- Base de données SQL
- API reliée à l'application Java

## Fonctionnalités
- Catalogue organisé en catégories : décorations, bijoux et satin
- Fiches produits détaillées
- Création de compte, connexion et déconnexion
- Profil utilisateur avec modification des informations personnelles
- Panier : ajout, retrait et ajustement des quantités
- Passage de commande avec mise à jour automatique des stocks dans la base de données
- Historique des commandes
- Gestion des rôles : interface d'administration accessible uniquement avec un compte administrateur, comptes clients pour les autres utilisateurs

## Lancer le projet
1. Cloner le dépôt puis installer les dépendances :
   ```bash
   composer install
2.Copier .env en .env.local et y renseigner votre DATABASE_URL.
3.Créer la base et appliquer les migrations :
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
4. Démarrer le serveur :
    symfony server:start
    
Auteure

Julie Bregman
