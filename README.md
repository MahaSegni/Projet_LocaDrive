# Projet LocaDrive - API de Location de Véhicules

## À propos du projet

LocaDrive est une API RESTful développée avec Symfony qui permet la gestion de location de véhicules. Ce projet suit les principes de la Screaming Architecture avec une séparation claire entre les cas d'utilisation (use cases) et les contrôleurs.

## Technologies utilisées

- **Symfony** - Framework PHP
- **Doctrine ORM** - Pour la persistance des données
- **JWT Authentication** - Pour l'authentification des utilisateurs
- **PHP 8.x** - Avec typages stricts

## Structure du projet

Le projet suit une architecture "Screaming" avec une séparation claire des responsabilités:

### Couche Domain / Entités

Représente les objets métier et les règles associées:

- `User` - Utilisateurs (clients et administrateurs)
- `Vehicle` - Véhicules disponibles à la location
- `Reservation` - Réservations de véhicules
- `Commande` - Commandes (panier) regroupant les réservations
- `Enum/StatutCommande` - États possibles d'une commande (CART, VALIDEE, etc.)

### Couche Repository

Chaque entité dispose d'un repository pour gérer la persistance:

- `VehicleRepository` - Gestion des véhicules
- `ReservationRepository` - Gestion des réservations
- `CommandeRepository` - Gestion des commandes
- `UserRepository` - Gestion des utilisateurs

### Couche Application / Use Cases

Implémention des cas d'utilisation métier (non visibles dans le code fourni mais référencés dans les contrôleurs):

- Gestion des véhicules (ajout, modification, suppression)
- Création et authentification des comptes
- Gestion des réservations et du panier
- Gestion des paiements et assurances

### Couche Controller

Points d'entrée de l'API:

- `VehicleController` - API de gestion des véhicules
- `UserController` - API de gestion des utilisateurs
- `CommandeController` - API de gestion des commandes et réservations
- `LoginController` - API d'authentification

## Fonctionnalités

### Gestion des utilisateurs

- Création de compte utilisateur
- Authentification avec JWT
- Gestion des profils

### Gestion des véhicules (administrateur)

- Ajouter un véhicule
- Modifier un véhicule
- Supprimer un véhicule
- Lister tous les véhicules

### Gestion des réservations (client)

- Créer une commande (panier)
- Ajouter des véhicules à une réservation
- Retirer des véhicules d'une réservation
- Ajouter/retirer une assurance
- Choisir un mode de paiement
- Procéder au paiement

## Points forts du projet

- **Architecture Screaming** avec séparation claire des responsabilités
- **Modélisation du domaine** avec des entités riches encapsulant les règles métier
- **Validation des règles métier** au niveau des entités
- **Optimisation des performances Doctrine** avec des jointures explicites
- **Gestion fine des erreurs** avec des exceptions métier
- **Sécurité** avec authentification JWT et contrôle des accès

## Routes API

### Authentification

- `POST /api/login` - Connexion utilisateur
- `POST /account` - Création de compte client

### Gestion des véhicules (admin)

- `GET /api/vehicles` - Liste des véhicules
- `GET /api/vehicle/{id}` - Détails d'un véhicule
- `POST /api/vehicle` - Ajout d'un véhicule
- `PUT /api/vehicle/{id}` - Modification d'un véhicule
- `DELETE /api/vehicle/{id}` - Suppression d'un véhicule

### Gestion des commandes et réservations (client)

- `POST /api/commandes` - Création d'une commande
- `GET /api/commandes` - Liste des commandes du client
- `POST /api/commandes/{id}/reservations` - Ajout d'une réservation
- `DELETE /api/commandes/{commandeId}/reservations/{reservationId}` - Retrait d'une réservation
- `POST /api/commandes/{commandeId}/reservations/{reservationId}/assurance` - Ajout d'assurance
- `DELETE /api/commandes/{commandeId}/reservations/{reservationId}/assurance` - Retrait d'assurance
- `PUT /api/commandes/{id}/mode-paiement` - Modification du mode de paiement
- `POST /api/commandes/{id}/payer` - Paiement de la commande
- `PUT /api/commandes/{id}/confirmer` - Confirmation de la commande
- `PUT /api/commandes/{id}/annuler` - Annulation de la commande

### Gestion des utilisateurs (admin)

- `GET /api/admin/users` - Liste des utilisateurs
- `GET /api/admin/users/{id}` - Détails d'un utilisateur
- `PUT /api/admin/users/{id}` - Modification d'un utilisateur
- `DELETE /api/admin/users/{id}` - Suppression d'un utilisateur

## Règles métier

### Véhicules
- Le modèle, la marque et le tarif sont obligatoires
- Le tarif doit être supérieur à 0

### Utilisateurs
- Le mot de passe doit faire plus de 8 caractères et contenir 4 chiffres et 4 lettres
- L'email, nom, prénom et date d'obtention permis sont obligatoires
- L'email doit être unique pour chaque compte

### Réservations
- Les dates de début et de fin doivent être dans le futur
- La date de fin doit être après la date de début
- Le client doit être authentifié pour créer une réservation
- Les modifications ne sont possibles que si la commande est en statut CART

### Assurance
- Une seule assurance est possible par réservation
- L'assurance proposée est unique et le prix aussi (20€)
- Ajout/retrait uniquement en statut CART

### Paiement
- Choix entre CB et PayPal
- Modification uniquement en statut CART
- Finalisation uniquement en statut CART

## Installation

```bash
# Cloner le dépôt
git clone https://github.com/MahaSegni/Projet_LocaDrive.git

# Lancer Docker
docker compose up -d

# Se connecter au bash
docker compose exec php bash

# Installer les dépendances
composer install

# Créer la migration
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

```
