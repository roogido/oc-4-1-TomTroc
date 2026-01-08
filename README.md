# TomTroc

TomTroc est une application web permettant à des utilisateurs d’échanger des livres entre particuliers.
Chaque utilisateur peut proposer ses livres à l’échange, consulter ceux des autres membres et entrer en contact
via une messagerie interne.

Ce projet a été réalisé dans le cadre de la formation **OpenClassrooms – Développeur d’application Full-Stack**
(PHP), dans une logique **MVP** et en respectant strictement les spécifications fonctionnelles fournies.

---

## Contexte du projet

- Formation : **Développeur d’application Full-Stack** (OpenClassrooms)
- Projet : TomTroc (plateforme d’échange de livres)
- Objectif : concevoir une application web fonctionnelle, maintenable et sécurisée
- Approche : **MVP**, sans fonctionnalités hors périmètre (ex. : administration avancée)

---

## Description technique

L’application repose sur une architecture **MVC** développée en **PHP orienté objet**, sans framework,
en respectant les bonnes pratiques suivantes :

- Architecture MVC claire (Controllers / Views / Models)
- Programmation orientée objet (POO)
- Séparation des responsabilités (Controllers, Services, Repositories)
- Accès aux données via PDO (requêtes préparées)
- Base de données relationnelle **MariaDB / MySQL**
- Gestion des sessions et authentification sécurisée
- Messagerie interne entre utilisateurs
- Upload d’avatars et d’images de livres
- Interface responsive conforme aux maquettes Figma fournies

### Technologies utilisées

- PHP 8.x
- MySQL / MariaDB
- HTML5 / CSS3
- JavaScript (vanilla)
- PDO (requêtes préparées)
- Architecture MVC maison

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

## Installation et déploiement

### Configuration de la base de données

Pour des raisons de sécurité, les identifiants de connexion à la base de données ne sont **pas fournis**
dans le dépôt GitHub.

Il sera donc nécessaire de définir les accès à la base de données.

---

### 1. Récupération des fichiers de configuration

Décompresser l’archive fournie dans le dossier `config` :

```bash
unzip config/tomtroc.zip
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

### Données de démonstration

Des données factices sont fournies en base de données :
- utilisateurs
- livres
- messages

Le fichier suivant contient les identifiants de connexion pour les utilisateurs de test :
`docs/notes/Utilisateurs_applicatifs.txt`

Ces comptes permettent de tester l’ensemble des fonctionnalités de l’application
(authentification, échanges, messagerie).

---

## Fonctionnalités implémentées
- Inscription et authentification des utilisateurs
- Gestion du profil utilisateur
- Ajout, modification et consultation de livres
- Messagerie interne entre utilisateurs
- Upload d’avatars et d’images de livres
- Interface responsive conforme aux maquettes fournies

## Remarques

Le projet respecte strictement le périmètre fonctionnel défini dans les spécifications.
Les fonctionnalités optionnelles (ex. : partie administration avancée, champs prénom/nom)
n’ont volontairement pas été implémentées afin de rester cohérent avec le MVP attendu.

## Auteur

Projet réalisé par **Salem Hadjali** dans le cadre de la formation 
**Développeur d’application full-stack / OpenClassrooms**.