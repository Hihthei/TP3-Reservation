### TP-03-SYS-RESERV-CAL : Système de Réservation avec Calendrier

#### **Cahier des Charges**

---

### **Descriptif du Projet**

Le projet consiste à développer un système de réservation en ligne avec un calendrier intégré. Ce système permettra aux utilisateurs de créer un compte, de prendre des rendez-vous, de gérer leurs informations personnelles, et de vérifier la disponibilité des créneaux horaires. Le système doit être sécurisé, facile à utiliser, et respecter les contraintes techniques et fonctionnelles définies.

---

### **Descriptif Fonctionnel**

1. **Gestion des Utilisateurs** : 
   - **Création de compte** : (2 points)  
     - Formulaire d'inscription avec nom, prénom, date de naissance, adresse postale, numéro de téléphone, email, et mot de passe.
     - Vérification de l'unicité de l'email.
     - Envoi d'un email de vérification pour activer le compte.
   - **Connexion et déconnexion** : (2 points)  
     - Formulaire de connexion avec email et mot de passe.
     - Redirection vers le profil après connexion réussie.
   - **Modification des informations personnelles** :  (2 points)
     - Possibilité de modifier le nom, prénom, date de naissance, adresse postale, numéro de téléphone, et email.
     - Vérification de l'unicité de l'email lors de la modification.
   - **Suppression du compte** :  (2 points)
     - Possibilité de supprimer le compte.
     - Suppression de toutes les données associées en base de données.

2. **Gestion des Rendez-vous** :
   - **Prise de rendez-vous** : (2 points)  
     - Calendrier interactif pour la sélection des dates et heures.
     - Vérification de la disponibilité des créneaux horaires.
     - Enregistrement du rendez-vous si le créneau est disponible.
   - **Affichage des rendez-vous** : (2 points)  
     - Affichage des rendez-vous pris par l'utilisateur.
   - **Annulation de rendez-vous** :  (2 points)   
     - Possibilité d'annuler un rendez-vous pris.
     - Libération du créneau horaire pour d'autres utilisateurs.

3. **Sécurité** :
   - **Protection contre les attaques CSRF** : (1 points)
     - Ajout de tokens CSRF pour sécuriser les formulaires.
   - **Hachage des mots de passe** :  (1 points)
     - Utilisation de `password_hash()` pour hasher les mots de passe avant de les stocker.
   - **Vérification de l'unicité de l'email** :  (1 points)
     - Vérification de l'unicité de l'email lors de l'inscription et de la modification du profil.
   - **Protection contre les attaques XSS et SQL Injection** : (1 points)

4. **Interface Utilisateur** :
   - **Calendrier interactif** :  (1 points)
     - Interface utilisateur intuitive pour la sélection des dates et heures.
   - **Formulaire de contact** :  (1 points)
     - Formulaire pour les demandes de renseignements.

---

### **Contraintes Techniques**

1. **Technologies** :
   - Frontend : Bootstrap.
   - Backend : PHP procédural.
   - Base de données : MySQL.

2. **Sécurité** :
   - Protection contre les attaques XSS et SQL Injection.
   - Utilisation de tokens CSRF pour les formulaires.
   - Validation des emails et des numéros de téléphone.

3. **Performance** :
   - Gestion efficace des créneaux horaires pour éviter les conflits de rendez-vous.
   - Optimisation des requêtes SQL pour une meilleure performance.

---

### **User Stories**

1. **En tant qu'utilisateur, je veux pouvoir créer un compte** :
   - **Cas d'acceptation** :
     - Le formulaire d'inscription demande un nom, un prénom, une date de naissance, une adresse postale, un numéro de téléphone, un email, et un mot de passe.
     - L'email doit être unique.
     - Un email de vérification est envoyé pour activer le compte.

2. **En tant qu'utilisateur, je veux pouvoir me connecter** :
   - **Cas d'acceptation** :
     - Le formulaire de connexion demande un email et un mot de passe.
     - Si les informations sont correctes, l'utilisateur est redirigé vers son profil.

3. **En tant qu'utilisateur, je veux pouvoir modifier mes informations personnelles** :
   - **Cas d'acceptation** :
     - L'utilisateur peut modifier son nom, prénom, date de naissance, adresse postale, numéro de téléphone, et email.
     - L'email doit être unique.

4. **En tant qu'utilisateur, je veux pouvoir prendre un rendez-vous** :
   - **Cas d'acceptation** :
     - L'utilisateur peut sélectionner une date et une heure dans un calendrier interactif.
     - Le système vérifie la disponibilité du créneau horaire.
     - Si le créneau est disponible, le rendez-vous est enregistré.

5. **En tant qu'utilisateur, je veux pouvoir annuler un rendez-vous** :
   - **Cas d'acceptation** :
     - L'utilisateur peut annuler un rendez-vous pris.
     - Le créneau horaire est libéré et disponible pour d'autres utilisateurs.

6. **En tant qu'utilisateur, je veux pouvoir supprimer mon compte** :
   - **Cas d'acceptation** :
     - L'utilisateur peut supprimer son compte.
     - Toutes les données associées à l'utilisateur sont supprimées de la base de données.

---

### **Livrables**

1. **Use Case** :
   - Description détaillée des cas d'utilisation avec les acteurs et les scénarios.

2. **Diagramme de Séquence** :
   - Diagramme illustrant les interactions entre l'utilisateur et le système pour les principales fonctionnalités (création de compte, prise de rendez-vous, modification des informations personnelles, suppression de compte).

3. **Schéma de Base de Données** :
   - Schéma illustrant les tables et leurs relations avec les cardinalités.

4. **Projet Versionné** :
   - Projet versionné avec des commits réguliers et clairs.

# Système de Réservation avec Calendrier

## Description du Projet

Ce projet est un système de réservation en ligne avec un calendrier intégré, similaire à Google Agenda. Il permet aux utilisateurs de créer un compte, de gérer leurs événements et rendez-vous, de modifier leurs informations personnelles, et de visualiser facilement leur emploi du temps.

L'application est développée en PHP avec une base de données MySQL et utilise Bootstrap pour l'interface utilisateur.

## Fonctionnalités

### Gestion des Utilisateurs

- **Création de compte ✅**
  - Formulaire d'inscription complet avec:
    - Nom, prénom
    - Date de naissance
    - Adresse postale
    - Numéro de téléphone
    - Email (vérification d'unicité)
    - Mot de passe sécurisé

- **Connexion et déconnexion ✅**
  - Système d'authentification sécurisé
  - Protection CSRF
  - Redirection vers le calendrier après connexion

- **Modification des informations personnelles ✅**
  - Interface de profil pour mettre à jour les informations
  - Vérification de l'unicité de l'email
  - Changement de mot de passe

- **Suppression de compte ✅**
  - Possibilité de supprimer définitivement le compte utilisateur

### Gestion des Événements

- **Création d'événements ✅**
  - Calendrier interactif hebdomadaire
  - Sélection de créneaux horaires
  - Ajout de titre et description
  - Vérification de disponibilité

- **Affichage des événements ✅**
  - Vue calendrier avec tous les événements de l'utilisateur
  - Page dédiée avec liste des événements
  - Visualisation détaillée d'un événement

- **Modification d'événements ✅**
  - Possibilité de modifier le titre et la description
  - Interface intuitive

- **Suppression d'événements ✅**
  - Possibilité de supprimer un événement

### Sécurité

- **Protection CSRF ✅**
  - Jetons CSRF pour les formulaires
  - Validation côté serveur

- **Hachage des mots de passe ✅**
  - Utilisation de `password_hash()` pour sécuriser les mots de passe

- **Prévention des injections SQL ✅**
  - Utilisation de requêtes préparées PDO

- **Protection XSS ✅**
  - Échappement des données utilisateur avec `htmlspecialchars()`

## Structure du Projet

### Pages Principales

- **index.php** - Page de connexion
- **inscription.php** - Création de compte
- **calendar.php** - Vue principale du calendrier
- **events.php** - Gestion des événements
- **profil.php** - Gestion du profil utilisateur

### Dossier Asset

- **php/** - Scripts PHP de traitement
  - config.php - Configuration de la base de données
  - connexion.php - Traitement de la connexion
  - events.php - Gestion des événements
  - inscription.php - Traitement de l'inscription
  - delete.php - Suppression de compte

- **css/** - Feuilles de style
  - style.css - Styles personnalisés
  - dashboard.css - Styles pour le tableau de bord

## Base de Données

Le projet utilise une base de données MySQL avec deux tables principales:

### Table `users`

- id (PK)
- nom
- prenom
- email (unique)
- mot_de_passe (haché)
- date_de_naissance
- adresse_postale
- telephone

### Table `events`

- id (PK)
- user_id (FK)
- titre
- description
- date_heure_debut
- date_heure_fin

## Installation et Configuration

1. Cloner le dépôt
2. Créer une base de données MySQL nommée "calendar"
3. Importer la structure de base de données (script SQL fourni séparément)
4. Configurer les paramètres de connexion dans `Asset/php/config.php` selon votre environnement
5. Déployer les fichiers sur un serveur PHP (version 7.4+ recommandée)

## Capture d'écran

(Ajoutez des captures d'écran de votre application ici)

## Utilisation

1. Accédez à la page d'accueil pour vous connecter ou créer un compte
2. Une fois connecté, vous serez redirigé vers le calendrier
3. Cliquez sur un créneau horaire pour créer un événement
4. Gérez vos événements depuis la page "Mes Événements"
5. Modifiez votre profil depuis la page "Mon Profil"

## Auteur

Hihthei/BOUILLON_Célin ESIEA - Laval - 3A.

## Licence

Ce projet est distribué sous licence MIT.