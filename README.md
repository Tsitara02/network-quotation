# Application de Devis Réseau en PHP

Ce projet est une application web simple développée en **PHP**, **MySQL/MariaDB**, **HTML**, **CSS** et **JavaScript**.
Elle permet de gérer des clients, des matériels et de créer des devis pour des prestations réseau, installation Wi-Fi, Starlink, caméras IP ou autres services techniques.

## Objectif du projet

L’objectif de cette application est de faciliter la création rapide de devis professionnels pour les services réseau et sécurité.

L’application permet notamment de :

* enregistrer des clients ;
* gérer une liste de matériels ;
* créer un nouveau devis ;
* ajouter plusieurs lignes dans un devis ;
* calculer automatiquement les montants ;
* afficher la liste des devis enregistrés ;
* préparer une base pour générer ou imprimer les devis.

## Technologies utilisées

* PHP
* MySQL / MariaDB
* HTML5
* CSS3
* JavaScript
* Apache ou serveur local PHP
* phpMyAdmin ou terminal MariaDB

## Structure du projet

```text
network-quotation/
│
├── clients.php
├── config.php
├── enregistrer_devis.php
├── index.php
├── liste_devis.php
├── materiels.php
├── nouveau_devis.php
│
├── css/
│   └── style.css
│
├── js/
│   └── script.js
│
└── images/
    └── logo.jpeg
```

## Description des fichiers

### `index.php`

Page d’accueil de l’application.
Elle sert de point d’entrée principal et permet d’accéder aux différentes fonctionnalités.

### `config.php`

Fichier de configuration de la base de données.
Il contient les informations de connexion à MySQL ou MariaDB.

Exemple :

```php
<?php
$host = "localhost";
$dbname = "estimatedb";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
```

### `clients.php`

Page permettant d’ajouter, afficher ou gérer les clients.

Un client peut contenir par exemple :

* nom ;
* téléphone ;
* adresse ;
* email ;
* type de client : particulier, entreprise, hôtel, école, ONG, etc.

### `materiels.php`

Page de gestion des matériels utilisés dans les devis.

Exemples de matériels :

* routeur MikroTik ;
* câble réseau ;
* connecteurs RJ45 ;
* caméra IP ;
* switch ;
* antenne Wi-Fi ;
* support Starlink ;
* main-d’œuvre ou prestation technique.

### `nouveau_devis.php`

Page principale pour créer un nouveau devis.

Elle permet de sélectionner un client, d’ajouter des lignes de devis et de calculer les montants.

Chaque ligne peut contenir :

* désignation ;
* type de ligne ;
* quantité ;
* prix unitaire ;
* total HT ;
* TVA ;
* total TTC.

### `enregistrer_devis.php`

Fichier chargé d’enregistrer le devis dans la base de données.

Il reçoit les données envoyées par le formulaire de création de devis et les insère dans les tables correspondantes.

### `liste_devis.php`

Page qui affiche tous les devis déjà enregistrés.

Elle peut être améliorée pour permettre :

* la recherche d’un devis ;
* le filtrage par client ;
* l’affichage du détail ;
* l’impression ;
* l’export PDF.

### `css/style.css`

Fichier de style de l’application.

Il permet de personnaliser :

* les couleurs ;
* les tableaux ;
* les boutons ;
* les formulaires ;
* la mise en page responsive.

### `js/script.js`

Fichier JavaScript utilisé pour rendre le formulaire de devis dynamique.

Il peut gérer :

* l’ajout de nouvelles lignes ;
* la suppression de lignes ;
* le calcul automatique des totaux ;
* le calcul de la TVA ;
* le calcul du total général.

## Installation du projet

### 1. Copier le projet

Copier le dossier du projet dans le répertoire web local.

Sous Linux avec Apache :

```bash
sudo cp -r network-quotation /var/www/html/
```

Ou si le projet est déjà dans `/var/www/html/`, vérifier simplement son emplacement :

```bash
ls /var/www/html/network-quotation
```

### 2. Démarrer Apache et MariaDB

Sous Fedora :

```bash
sudo systemctl start httpd
sudo systemctl start mariadb
```

Pour activer les services au démarrage :

```bash
sudo systemctl enable httpd
sudo systemctl enable mariadb
```

### 3. Créer la base de données

Se connecter à MariaDB :

```bash
sudo mariadb
```

Créer la base :

```sql
CREATE DATABASE estimatedb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

Utiliser la base :

```sql
USE estimatedb;
```

## Exemple de structure SQL

### Table `clients`

```sql
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    telephone VARCHAR(50),
    email VARCHAR(150),
    adresse TEXT,
    type_client VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Table `materiels`

```sql
CREATE TABLE materiels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    designation VARCHAR(255) NOT NULL,
    categorie VARCHAR(100),
    prix_unitaire DECIMAL(10,2) NOT NULL DEFAULT 0,
    unite VARCHAR(50) DEFAULT 'unité',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Table `devis`

```sql
CREATE TABLE devis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    numero_devis VARCHAR(50),
    date_devis DATE,
    total_ht DECIMAL(10,2) DEFAULT 0,
    tva DECIMAL(10,2) DEFAULT 0,
    total_ttc DECIMAL(10,2) DEFAULT 0,
    statut VARCHAR(50) DEFAULT 'brouillon',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);
```

### Table `lignes_devis`

```sql
CREATE TABLE lignes_devis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    devis_id INT NOT NULL,
    designation VARCHAR(255) NOT NULL,
    type_ligne VARCHAR(30),
    quantite DECIMAL(10,2) DEFAULT 1,
    prix_unitaire DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (devis_id) REFERENCES devis(id) ON DELETE CASCADE
);
```

## Configuration de la base de données

Dans le fichier `config.php`, modifier les informations selon votre configuration locale :

```php
$host = "localhost";
$dbname = "estimatedb";
$username = "root";
$password = "";
```

Si vous utilisez un utilisateur MariaDB différent, adaptez :

```php
$username = "votre_utilisateur";
$password = "votre_mot_de_passe";
```

## Lancer le projet

Dans le navigateur, ouvrir :

```text
http://localhost/network-quotation/
```

Ou directement :

```text
http://localhost/network-quotation/index.php
```

Pour voir la liste des devis :

```text
http://localhost/network-quotation/liste_devis.php
```

## Fonctionnalités actuelles

* Gestion des clients
* Gestion des matériels
* Création de devis
* Ajout de plusieurs lignes dans un devis
* Calcul des montants
* Enregistrement dans MariaDB
* Consultation de la liste des devis

## Améliorations possibles

Le projet peut encore être amélioré avec les fonctionnalités suivantes :

* génération automatique du numéro de devis ;
* export PDF du devis ;
* impression directe ;
* modification d’un devis existant ;
* suppression sécurisée d’un devis ;
* ajout du logo de l’entreprise sur le devis ;
* gestion de la TVA ;
* ajout d’un taux de bénéfice variable ;
* tableau de bord avec statistiques ;
* recherche et filtre dans la liste des devis ;
* authentification utilisateur ;
* sauvegarde automatique ;
* design responsive pour téléphone et tablette.

## Exemple de calcul

Pour une ligne de devis :

```text
Quantité = 2
Prix unitaire = 150 000 Ar
Total = 2 × 150 000
Total = 300 000 Ar
```

Pour le total général :

```text
Total HT = somme des lignes
TVA = Total HT × taux TVA
Total TTC = Total HT + TVA
```

## Sécurité à prévoir

Pour rendre l’application plus fiable, il est recommandé de :

* utiliser PDO avec requêtes préparées ;
* valider les données des formulaires ;
* empêcher les champs vides importants ;
* protéger les pages sensibles ;
* éviter l’affichage direct des erreurs en production ;
* ajouter une authentification ;
* faire des sauvegardes régulières de la base de données.

## Auteur

Projet développé par **Tsitara Nazara**.

Ce projet peut servir de base pour une application professionnelle de création de devis pour les services réseau, informatique, sécurité, Starlink, Wi-Fi et caméras IP.

## Licence

Projet personnel ou pédagogique.
Vous pouvez l’adapter selon vos besoins.
