# Film Tracker

Application web de suivi de films développée avec Laravel.

## Technologies utilisées

- **PHP 8.3**
- **Laravel 13** — framework PHP
- **MySQL** — base de données
- **Tailwind CSS** — styles
- **Vite** — compilation des assets
- **API OMDB** — récupération des informations sur les films

---

## Prérequis

Avant de commencer, vérifier que ces outils sont installés :

- PHP 8.3 ou supérieur
- Composer
- Node.js et npm
- MySQL

---

## Installation

### 1. Installer PHP 8.3

```bash
sudo apt install php8.3 php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl php8.3-mysql
```

### 2. Installer Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 3. Installer Node.js et npm

```bash
sudo apt install nodejs npm
```

### 4. Installer MySQL

```bash
sudo apt install mysql-server
```

---

## Lancer le projet

### 1. Installer les dépendances PHP

```bash
composer install
```

### 2. Installer les dépendances JavaScript

```bash
npm install
```

### 3. Configurer le fichier d'environnement

```bash
cp .env.example .env
```

Ouvrir le fichier `.env` et renseigner les valeurs :

```env
DB_DATABASE=MovieTracker
DB_USERNAME=root
DB_PASSWORD=ton_mot_de_passe

OMDB_API_KEY=ta_cle_api
```

### 4. Générer la clé de l'application

```bash
php artisan key:generate
```

### 5. Créer la base de données

```bash
sudo mysql
CREATE DATABASE MovieTracker;
EXIT;
```

### 6. Exécuter les migrations

```bash
php artisan migrate
```

### 7. Lancer le serveur

Dans deux terminaux séparés :

```bash
# Terminal 1 — serveur PHP
php artisan serve
```

```bash
# Terminal 2 — compilation des assets
npm run dev
```

L'application est accessible sur : **[http://localhost:8000](http://localhost:8000)**
