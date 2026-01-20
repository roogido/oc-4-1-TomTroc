# TomTroc

TomTroc est une application web permettant à des utilisateurs d’échanger des livres entre particuliers.
Chaque utilisateur peut proposer ses livres à l’échange, consulter ceux des autres membres et entrer en contact
via une messagerie interne.

Ce projet a été réalisé dans le cadre de la formation **OpenClassrooms – Développeur d’application Full-Stack**
(PHP), dans une logique **MVP** (Minimal Viable Product) et en respectant les spécifications fonctionnelles fournies.

---

## Contexte du projet

- Formation : **Développeur d’application Full-Stack** (OpenClassrooms)
- Projet : TomTroc (plateforme d’échange de livres)
- Objectif : concevoir une application web fonctionnelle, maintenable et sécurisée
- Approche : **MVP**, avec une interface d’administration limitée aux besoins essentiels

---

## Description technique

L’application repose sur une architecture **MVC** développée en **PHP orienté objet**, sans framework,
en respectant les bonnes pratiques suivantes :

- Architecture MVC claire (Controllers / Views / Models)
- Programmation orientée objet (POO)
- Séparation des responsabilités (Controllers, Services, Repositories)
- Accès aux données via **PDO** (requêtes préparées)
- Base de données relationnelle **MariaDB**
- Authentification et gestion des sessions sécurisées
- Protection **CSRF sur l’ensemble des formulaires POST**
- Messagerie interne entre utilisateurs
- Upload d’avatars et d’images de livres
- Interface responsive conforme aux maquettes Figma fournies
- **Interface d’administration dédiée** (gestion des utilisateurs et des livres)

---

### Fonctionnalités d’administration

Une section **Administration** est accessible aux comptes disposant des droits nécessaires :

- Tableau de bord administrateur
- Gestion des utilisateurs (activation / désactivation)
- Gestion des livres (visibilité, disponibilité)
- Pagination des listes
- Interface responsive (desktop, tablette, mobile)
- Accès sécurisé par rôle (admin uniquement)

---

### Technologies utilisées

- PHP 8.2.12
- MariaDB (compatible MySQL)
- HTML5 / CSS3
- JavaScript (vanilla)
- PDO (requêtes préparées)
- Architecture MVC maison

### Environnement de développement

- XAMPP (Apache, PHP, MariaDB)

---

## 📁 Structure du projet

```text
.
├── app
│ ├── Controllers
│ ├── Core
│ ├── Models
│ ├── Repositories
│ ├── Routes
│ ├── Service
│ └── View
├── config
├── docs
├── public
│ ├── assets
│ └── uploads
├── sql
└── views
```

---

## Récupération du projet

Vous pouvez récupérer le projet de l’une des manières suivantes.

### Option 1 : Cloner le dépôt (SSH)
```bash
git clone git@github.com:roogido/oc-4-1-TomTroc.git
```

### Option 2 : Cloner le dépôt (HTTPS)
```bash
git clone https://github.com/roogido/oc-4-1-TomTroc.git
```

### Option 3 : Télécharger l’archive ZIP
```bash
https://github.com/roogido/oc-4-1-TomTroc/archive/refs/heads/main.zip
```

## Installation et déploiement

### Configuration de la base de données

Pour des raisons de sécurité, les identifiants de connexion à la base de données ne sont **pas fournis**
dans le dépôt GitHub.

Il sera donc nécessaire de définir les accès à la base de données.

---

### 1. Récupération des fichiers de configuration

Décompresser l’archive fournie dans le dossier `sql` :

```bash
unzip sql/tomtroc.zip
```

### 2. Création et import de la base de données

Créer une base de données nommée tomtroc, puis importer le dump SQL fourni :
```bash
mysql -u USERNAME -p tomtroc < sql/tomtroc.sql
```

Adapter :

- USERNAME selon votre configuration MySQL
- le nom de la base si nécessaire


### 3. Paramétrage de l’accès à la base de données

Renseigner les informations de connexion dans le fichier :
`config/database.php`

Compléter les champs selon votre environnement :
```php
return [
    'driver'   => 'mysql',
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'database' => 'tomtroc',
    'username' => 'CHANGE_ME',
    'password' => 'CHANGE_ME',
];
```

### 4. Lancement du projet

Configurer votre serveur web (Apache / Nginx) pour pointer sur le dossier :
`public/`

Puis accéder à l’application via votre navigateur.
Si l’installation est correcte, la page d’accueil TomTroc s’affiche sans erreur.

### Données de démonstration

Des données factices sont fournies en base de données :
- utilisateurs
- livres
- messages

Le fichier suivant contient les identifiants de connexion pour les utilisateurs de test :
`docs/demo_users.txt`

### Comptes principaux de démonstration

#### Administrateur
- Pseudo : admin
- Login : admin@tomtroc.test
- Mot de passe : admin1

#### Utilisateurs standards
- Pseudo : CamilleClubLit
- Login : camille.clublit@tomtroc.test
- Mot de passe : password
- Pseudo : Alexlecture
- Login : alexlecture@tomtroc.test
- Mot de passe : password
- Pseudo : Lotrfanclub67
- Login : lotrfanclub67@tomtroc.test
- Mot de passe : password

Ces comptes permettent de tester :
- l’authentification
- les échanges de livres
- la messagerie
- les fonctionnalités d’administration (selon le rôle)

---

## Fonctionnalités implémentées
- Inscription et authentification des utilisateurs
- Gestion du profil utilisateur
- Ajout, modification et consultation de livres
- Messagerie interne entre utilisateurs
- Upload d’avatars et d’images de livres
- Interface responsive conforme aux maquettes fournies
- Protection CSRF sur tous les formulaires POST
- Interface d’administration (utilisateurs & livres)

## Remarques

Le projet respecte strictement le périmètre fonctionnel défini dans les spécifications.
Les fonctionnalités optionnelles (ex. : recherche avancée, champs prénom/nom)
n’ont volontairement pas été implémentées afin de rester cohérent avec le MVP attendu.

## Auteur

Projet réalisé par **Salem Hadjali** dans le cadre de la formation 
**Développeur d’application full-stack / OpenClassrooms**.