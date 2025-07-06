\# 📘 Documentation du projet — Application bancaire PHP/MySQL

## 🧱 1. Architecture du projet

Le projet repose sur une architecture simple à deux machines virtuelles :

| VM             | Rôle                       | Technologies principales                       |
| -------------- | -------------------------- | ---------------------------------------------- |
| \*\*VMApp\*\*  | Serveur applicatif         | PHP brut (partiellement orienté objet), Apache |
| \*\*VMBack\*\* | Serveur de base de données | MySQL 8.0                                      |

---

## ⚙️ 2. Fonctionnement général

- L’application PHP s’exécute sur \*\*VMApp\*\* et communique avec la base de données distante sur \*\*VMBack\*\*.
- Elle permet d’exécuter des opérations bancaires (ex. : virements, consultations de comptes) via une interface web.
- Le code est écrit en PHP brut, avec une structure partiellement orientée objet pour la gestion de la base de données et des sessions.

---

## 🔐 3. Sécurité mise en place

### 🔒 Sécurisation de la base de données

- Accès MySQL restreint à l’IP de VMApp uniquement (via \`bind-address\` et pare-feu)
- Utilisateur MySQL dédié avec droits limités (pas de \`GRANT\`, pas de \`DROP\`)
- Mot de passe fort et non stocké en clair dans le code (utilisation de fichiers \`config.php\`)

### 🔒 Sécurisation de l’application PHP

- Requêtes SQL préparées avec \`PDO\` pour éviter les injections SQL
- Aucune donnée sensible dans les URL (utilisation de \`POST\` pour les formulaires)
- Sessions sécurisées avec \`session_start()\` et vérification d’authentification sur chaque page protégée
- Accès restreint à l’application via pare-feu (VMApp n’est pas exposée à Internet)

---

## 🧠 4. Choix techniques : pourquoi PHP brut ?

### ✅ Simplicité et maîtrise

- PHP brut permet une compréhension fine de chaque couche de l’application.
- Aucun framework tiers n’est requis, ce qui réduit la surface d’attaque et les dépendances.

### ✅ Sécurité maîtrisée

- Le développeur contrôle directement la logique métier, la validation des entrées, et la gestion des sessions.
- Moins de dépendances signifie moins de failles potentielles introduites par des bibliothèques externes.

### ✅ Adapté aux environnements sensibles

- Dans un contexte bancaire, l’utilisation de services tiers (frameworks, API externes, plateformes cloud) est souvent proscrite pour des raisons de conformité, de confidentialité et de souveraineté des données.
- PHP brut permet de rester 100 % autonome et conforme aux exigences de sécurité internes.

---

## 🚀 5. Déploiement du projet depuis GitHub

### 🧩 Prérequis

- Deux machines virtuelles Ubuntu (VMApp et VMBack)
- Accès SSH ou terminal sur chaque VM
- Git installé sur VMApp

### 🔹 Étapes sur VMBack (base de données)

1. Installer MySQL :

\`\`\`bash
sudo apt update
sudo apt install mysql-server

1.  Créer la base et l’utilisateur :

sql

CREATE DATABASE app_db;
CREATE USER 'app_user'@'192.168.56.102' IDENTIFIED BY 'mot_de_passe_fort';
GRANT SELECT, INSERT, UPDATE, DELETE ON app_db.\* TO 'app_user'@'192.168.56.102';
FLUSH PRIVILEGES;

### 🔹 Étapes sur VMApp (application PHP)

1.  Cloner le projet :

bash

git clone https://github.com/ton-utilisateur/ton-projet.git
cd ton-projet

1.  Installer Apache et PHP :

bash

sudo apt install apache2 php libapache2-mod-php php-mysql

1.  Configurer l’accès à la base :

Dans `config.php` :

php

define('DB_HOST', '192.168.56.101');
define('DB_NAME', 'app_db');
define('DB_USER', 'app_user');
define('DB_PASS', 'mot_de_passe_fort');

1.  Déployer les fichiers dans `/var/www/html` :

bash

sudo cp -r \* /var/www/html/
sudo systemctl restart apache2

## 🧠 6. Développement orienté objet

L’application utilise une structure **partiellement orientée objet**, notamment pour :

- La gestion de la connexion à la base de données via une classe `Database`
- La gestion des sessions et de l’authentification
- Une meilleure séparation des responsabilités dans le code

Cela permet une maintenance facilitée, une meilleure lisibilité, et une évolutivité future.

## 🔧 7. Évolutions souhaitables et possibles

AméliorationDescription🔐 Authentification forteAjouter une authentification à deux facteurs (2FA)📊 Journalisation des actionsEnregistrer les opérations critiques dans une table dédiée📈 Tableau de bordCréer une interface web pour visualiser les opérations📦 ConteneurisationDéploiement via Docker pour simplifier la gestion🔄 Sauvegardes automatiquesScript de backup régulier de la base MySQL📬 Alertes par mailEnvoi d’un mail si une action critique est détectée🔐 HTTPSAjouter un certificat SSL avec Let’s Encrypt ou auto-signé

## ✅ 8. Conclusion

Ce projet met en œuvre une architecture simple, robuste et sécurisée, adaptée à un environnement bancaire. Le choix de PHP brut, bien que minimaliste, est ici un choix stratégique pour garantir la maîtrise complète du code et la conformité aux exigences de sécurité. Même sans services tiers ni frameworks, l’application reste claire, modulaire et évolutive.

\---

Souhaites-tu que je t’aide à transformer ce fichier en PDF ou à le publier automatiquement sur un dépôt GitHub avec ton code ?

J'ai aussi fait une double authentification pour la securit

Parfait Cesario ! Je vais mettre à jour la documentation pour inclure ta mise en place de la double authentification (2FA), ce qui renforce considérablement la sécurité de ton application. Voici la section modifiée à intégrer dans le fichier Markdown :

## 🔐 3. Sécurité mise en place

### 🔒 Sécurisation de la base de données

- Accès MySQL restreint à l’IP de VMApp uniquement (via `bind-address` et pare-feu)
- Utilisateur MySQL dédié avec droits limités (pas de `GRANT`, pas de `DROP`)
- Mot de passe fort et non stocké en clair dans le code (utilisation de fichiers `config.php`)

### 🔒 Sécurisation de l’application PHP

- Requêtes SQL préparées avec `PDO` pour éviter les injections SQL
- Aucune donnée sensible dans les URL (utilisation de `POST` pour les formulaires)
- Sessions sécurisées avec `session_start()` et vérification d’authentification sur chaque page protégée
- Accès restreint à l’application via pare-feu (VMApp n’est pas exposée à Internet)

### 🔐 Double authentification (2FA)

- Mise en place d’un second facteur d’authentification après la saisie du mot de passe
- Génération d’un code temporaire à usage unique (OTP) envoyé par e-mail ou généré via une application (ex. : Google Authenticator)
- Vérification du code avant d’accéder à l’espace sécurisé
- Protection contre les attaques par vol de mot de passe ou session

Souhaites-tu que je t’aide à rédiger une section technique détaillée expliquant comment tu as implémenté la 2FA (ex. : génération de code, stockage temporaire, validation) ? Je peux aussi t’aider à intégrer cette documentation dans un dépôt GitHub ou à l’exporter en PDF.

Génère un fichier avec toutes les explications technique dedans

Voici un fichier complet au format Markdown contenant toutes les explications techniques de ton projet, y compris la mise en place de la double authentification (2FA), la structure orientée objet, la sécurité, et les instructions de déploiement. Tu peux copier ce contenu dans un fichier nommé par exemple :

📄 `documentation_projet_bancaire.md`

markdown

\# 📘 Documentation technique — Application bancaire PHP/MySQL

## 🧱 1. Architecture du projet

Le projet repose sur une architecture simple à deux machines virtuelles :

| VM             | Rôle                       | Technologies principales                       |
| -------------- | -------------------------- | ---------------------------------------------- |
| \*\*VMApp\*\*  | Serveur applicatif         | PHP brut (partiellement orienté objet), Apache |
| \*\*VMBack\*\* | Serveur de base de données | MySQL 8.0                                      |

---

## ⚙️ 2. Fonctionnement général

- L’application PHP s’exécute sur \*\*VMApp\*\* et communique avec la base de données distante sur \*\*VMBack\*\*.
- Elle permet d’exécuter des opérations bancaires (ex. : virements, consultations de comptes) via une interface web.
- Le code est écrit en PHP brut, avec une structure partiellement orientée objet pour la gestion de la base de données, des sessions et de l’authentification.

---

## 🔐 3. Sécurité mise en place

### 🔒 Sécurisation de la base de données

- Accès MySQL restreint à l’IP de VMApp uniquement (via \`bind-address\` et pare-feu)
- Utilisateur MySQL dédié avec droits limités (pas de \`GRANT\`, pas de \`DROP\`)
- Mot de passe fort et non stocké en clair dans le code (utilisation de fichiers \`config.php\`)

### 🔒 Sécurisation de l’application PHP

- Requêtes SQL préparées avec \`PDO\` pour éviter les injections SQL
- Aucune donnée sensible dans les URL (utilisation de \`POST\` pour les formulaires)
- Sessions sécurisées avec \`session_start()\` et vérification d’authentification sur chaque page protégée
- Accès restreint à l’application via pare-feu (VMApp n’est pas exposée à Internet)

### 🔐 Double authentification (2FA)

- Après la saisie du mot de passe, l’utilisateur doit entrer un code temporaire (OTP)
- Le code est généré côté serveur et envoyé par e-mail ou affiché via une application OTP (TOTP)
- Le code est stocké temporairement en session ou base de données avec une durée de validité courte (ex. : 5 minutes)
- L’accès à l’espace sécurisé est refusé tant que le code n’est pas validé

#### Exemple de logique 2FA :

1. L’utilisateur entre son identifiant et mot de passe
2. Si les identifiants sont valides, un code OTP est généré :
   \`\`\`php
   $otp = random\_int(100000, 999999);
   $\_SESSION\['otp'\] = $otp;
   $\_SESSION\['otp_expire'\] = time() + 300;
   mail($user_email, "Votre code de connexion", "Code : $otp");

3. L’utilisateur est redirigé vers une page `otp.php` pour entrer le code
4. Le code est vérifié :
5. php

if ($\_POST\['otp'\] == $\_SESSION\['otp'\] && time() < $\_SESSION\['otp_expire'\]) {
$\_SESSION\['authenticated'\] = true;
}

## 🧠 4. Choix techniques : pourquoi PHP brut ?

### ✅ Simplicité et maîtrise

- PHP brut permet une compréhension fine de chaque couche de l’application.
- Aucun framework tiers n’est requis, ce qui réduit la surface d’attaque et les dépendances.

### ✅ Sécurité maîtrisée

- Le développeur contrôle directement la logique métier, la validation des entrées, et la gestion des sessions.
- Moins de dépendances signifie moins de failles potentielles introduites par des bibliothèques externes.

### ✅ Adapté aux environnements sensibles

- Dans un contexte bancaire, l’utilisation de services tiers (frameworks, API externes, plateformes cloud) est proscrite pour des raisons de conformité, de confidentialité et de souveraineté des données.
- PHP brut permet de rester 100 % autonome et conforme aux exigences de sécurité internes.

## 🚀 5. Déploiement du projet depuis GitHub

### 🧩 Prérequis

- Deux machines virtuelles Ubuntu (VMApp et VMBack)
- Accès SSH ou terminal sur chaque VM
- Git installé sur VMApp

### 🔹 Étapes sur VMBack (base de données)

1.  Installer MySQL :

bash

sudo apt update
sudo apt install mysql-server

1.  Créer la base et l’utilisateur :

sql

CREATE DATABASE app_db;
CREATE USER 'app_user'@'192.168.56.102' IDENTIFIED BY 'mot_de_passe_fort';
GRANT SELECT, INSERT, UPDATE, DELETE ON app_db.\* TO 'app_user'@'192.168.56.102';
FLUSH PRIVILEGES;

### 🔹 Étapes sur VMApp (application PHP)

1.  Cloner le projet :

bash

git clone https://github.com/ton-utilisateur/ton-projet.git
cd ton-projet

1.  Installer Apache et PHP :

bash

sudo apt install apache2 php libapache2-mod-php php-mysql

1.  Configurer l’accès à la base :

Dans `config.php` :

php

define('DB_HOST', '192.168.56.101');
define('DB_NAME', 'app_db');
define('DB_USER', 'app_user');
define('DB_PASS', 'mot_de_passe_fort');

1.  Déployer les fichiers dans `/var/www/html` :

bash

sudo cp -r \* /var/www/html/
sudo systemctl restart apache2

## 🧩 6. Structure orientée objet

L’application utilise une structure partiellement orientée objet, notamment pour :

- La gestion de la connexion à la base de données via une classe `Database`
- La gestion des sessions et de l’authentification
- Une meilleure séparation des responsabilités dans le code

### Exemple de classe `Database` :

php

class Database {
private $pdo;

    public function \_\_construct($host, $dbname, $user, $pass) {
        $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    }

    public function query($sql, $params = \[\]) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

}

## 🔧 7. Évolutions souhaitables et possibles

AméliorationDescription🔐 Authentification forteAjouter une authentification à deux facteurs (déjà en place ✅)📊 Journalisation des actionsEnregistrer les opérations critiques dans une table dédiée📈 Tableau de bordCréer une interface web pour visualiser les opérations📦 ConteneurisationDéploiement via Docker pour simplifier la gestion🔄 Sauvegardes automatiquesScript de backup régulier de la base MySQL📬 Alertes par mailEnvoi d’un mail si une action critique est détectée🔐 HTTPSAjouter un certificat SSL avec Let’s Encrypt ou auto-signé

## ✅ 8. Conclusion

Ce projet met en œuvre une architecture simple, robuste et sécurisée, adaptée à un environnement bancaire. Le choix de PHP brut, bien que minimaliste, est ici un choix stratégique pour garantir la maîtrise complète du code et la conformité aux exigences de sécurité. La mise en place d’une authentification à deux facteurs renforce encore la protection des accès.
