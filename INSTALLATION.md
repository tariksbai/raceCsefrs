# Guide d'installation – Plateforme de Formulaires Citoyens

## Prérequis serveur (VM Ubuntu 22.04 / Debian 12)

| Composant | Version minimale |
|-----------|-----------------|
| PHP | 8.2+ |
| Extensions PHP | `mbstring`, `xml`, `curl`, `zip`, `bcmath`, `pdo`, `pdo_mysql`, `tokenizer`, `fileinfo`, `openssl` |
| Composer | 2.x |
| MySQL | 8.0+ |
| Node.js | 18+ |
| npm | 9+ |
| Nginx (ou Apache) | dernière version stable |

---

## 1. Mise à jour du système

```bash
sudo apt update && sudo apt upgrade -y
```

---

## 2. Installation de PHP 8.2

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-tokenizer \
    php8.2-fileinfo php8.2-openssl php8.2-pdo

# Vérifier la version
php -v
```

---

## 3. Installation de Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

---

## 4. Installation de MySQL 8

```bash
sudo apt install -y mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql

# Sécuriser l'installation
sudo mysql_secure_installation
```

### Créer la base de données et l'utilisateur

```sql
sudo mysql -u root -p

CREATE DATABASE citizen_forms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'citizen_user'@'localhost' IDENTIFIED BY 'VotreMotDePasse';
GRANT ALL PRIVILEGES ON citizen_forms.* TO 'citizen_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 5. Installation de Node.js 18+

```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
node -v && npm -v
```

---

## 6. Installation de Nginx

```bash
sudo apt install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

---

## 7. Déploiement de l'application

### Cloner le dépôt

```bash
cd /var/www
sudo git clone <URL_DU_DEPOT> citizen-forms
sudo chown -R $USER:$USER /var/www/citizen-forms
cd /var/www/citizen-forms
```

### Installer les dépendances PHP

```bash
composer install --no-dev --optimize-autoloader
```

### Configurer l'environnement

```bash
cp .env.example .env
nano .env
```

Modifier les variables suivantes dans `.env` :

```dotenv
APP_NAME="Plateforme Formulaires Citoyens"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://votre-domaine.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=citizen_forms
DB_USERNAME=citizen_user
DB_PASSWORD=VotreMotDePasse

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Générer la clé d'application

```bash
php artisan key:generate
```

### Exécuter les migrations et les seeders

```bash
php artisan migrate --force
php artisan db:seed --force
```

> Cela crée les rôles (`admin`, `manager`, `citizen`), les permissions de base et l'utilisateur administrateur par défaut.

### Compiler les assets front-end

```bash
npm install
npm run build
```

### Configurer les permissions des dossiers

```bash
sudo chown -R www-data:www-data /var/www/citizen-forms/storage
sudo chown -R www-data:www-data /var/www/citizen-forms/bootstrap/cache
sudo chmod -R 775 /var/www/citizen-forms/storage
sudo chmod -R 775 /var/www/citizen-forms/bootstrap/cache
```

### Créer le lien symbolique pour le stockage public

```bash
php artisan storage:link
```

### Optimiser pour la production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 8. Configuration Nginx

Créer le fichier de configuration du vhost :

```bash
sudo nano /etc/nginx/sites-available/citizen-forms
```

Contenu du fichier :

```nginx
server {
    listen 80;
    server_name votre-domaine.com www.votre-domaine.com;
    root /var/www/citizen-forms/public;

    index index.php index.html;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Sécurité headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
}
```

Activer le site :

```bash
sudo ln -s /etc/nginx/sites-available/citizen-forms /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 9. Compte administrateur par défaut

Après l'exécution des seeders, un compte administrateur est disponible :

| Champ | Valeur |
|-------|--------|
| Email | `admin@example.com` |
| Mot de passe | `password` |

> **Changez immédiatement ce mot de passe** après la première connexion.

---

## 10. (Optionnel) HTTPS avec Let's Encrypt

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d votre-domaine.com -d www.votre-domaine.com
sudo systemctl reload nginx
```

---

## 11. Vérification finale

```bash
# Vérifier que l'application répond
curl -I http://votre-domaine.com

# Vérifier les logs en cas d'erreur
tail -f /var/www/citizen-forms/storage/logs/laravel.log
```

---

## Structure des rôles

| Rôle | Description |
|------|-------------|
| `admin` | Accès complet à toutes les fonctionnalités |
| `manager` | Création et gestion des formulaires |
| `citizen` | Accès aux formulaires publics uniquement |

## Dépannage courant

| Problème | Solution |
|----------|----------|
| Erreur 500 | Vérifier `storage/logs/laravel.log` |
| Permissions refusées | `sudo chmod -R 775 storage bootstrap/cache` |
| La base de données ne se connecte pas | Vérifier les credentials dans `.env` |
| Assets non chargés | Relancer `npm run build` |
| Cache obsolète | `php artisan cache:clear && php artisan config:clear` |
