# Installation complète - Citizen Forms Platform + LimeSurvey sur Ubuntu 22

Ce guide installe **deux applications web sur le même serveur Ubuntu 22 LTS**:
- **Citizen Forms Platform** (Laravel) → `https://forms.example.com`
- **LimeSurvey** (PHP) → `https://survey.example.com`

---

## 📋 Architecture

| Composant | Version | Port |
|-----------|---------|------|
| Ubuntu | 22.04 LTS | — |
| PHP | 8.2+ | — |
| MySQL | 8.0+ | 3306 |
| Nginx | Stable | 80, 443 |
| Node.js | 18+ | — |
| Composer | 2.x | — |

---

## 🚀 Phase 1 : Configuration système initiale

### 1.1 Mise à jour du système

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl wget git zip unzip htop vim
```

### 1.2 Configuration du fuseau horaire

```bash
sudo timedatectl set-timezone Africa/Casablanca  # Adaptez à votre fuseau
timedatectl
```

### 1.3 Configuration du swap (optionnel, si RAM < 2GB)

```bash
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
```

---

## 🔧 Phase 2 : Installation des services partagés

### 2.1 Installation de PHP 8.2 avec extensions

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

sudo apt install -y \
    php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-tokenizer \
    php8.2-fileinfo php8.2-openssl php8.2-pdo php8.2-gd php8.2-intl \
    php8.2-imap php8.2-ldap

# Vérifier la version
php -v
```

### 2.2 Installation de Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
composer --version
```

### 2.3 Installation de Node.js 18+

```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
node -v && npm -v
```

### 2.4 Installation et configuration de MySQL 8

```bash
sudo apt install -y mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql

# Sécuriser l'installation
sudo mysql_secure_installation
```

**Répondre aux questions** (recommandé) :
- Supprimer les utilisateurs anonymes : **Y**
- Désactiver la connexion root distante : **Y**
- Supprimer les bases de test : **Y**
- Recharger les tables des privilèges : **Y**

### 2.5 Créer les bases de données et utilisateurs

```bash
sudo mysql -u root -p << 'EOF'
-- Base pour Citizen Forms
CREATE DATABASE IF NOT EXISTS citizen_forms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'citizen_user'@'localhost' IDENTIFIED BY 'SecurePassword123!';
GRANT ALL PRIVILEGES ON citizen_forms.* TO 'citizen_user'@'localhost';

-- Base pour LimeSurvey
CREATE DATABASE IF NOT EXISTS limesurvey CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'lime_user'@'localhost' IDENTIFIED BY 'SecurePassword456!';
GRANT ALL PRIVILEGES ON limesurvey.* TO 'lime_user'@'localhost';

FLUSH PRIVILEGES;
EXIT;
EOF
```

### 2.6 Installation de Nginx

```bash
sudo apt install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx

# Tester
curl http://localhost
```

---

## 📦 Phase 3 : Installation de Citizen Forms Platform

### 3.1 Cloner et préparer l'application

```bash
cd /var/www
sudo git clone https://github.com/tariksbai/raceCsefrs.git citizen-forms
sudo chown -R $USER:$USER /var/www/citizen-forms
cd /var/www/citizen-forms
```

### 3.2 Installer les dépendances PHP

```bash
composer install --no-dev --optimize-autoloader
```

### 3.3 Configuration de l'environnement

```bash
cp .env.example .env
nano .env
```

**Configurer:**

```dotenv
APP_NAME="Plateforme Citoyenne"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://forms.example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=citizen_forms
DB_USERNAME=citizen_user
DB_PASSWORD=SecurePassword123!

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com        # Adaptez selon votre SMTP
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 3.4 Générer la clé et migrer

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
```

### 3.5 Compiler les assets front-end

```bash
npm install
npm run build
```

### 3.6 Permissions et liens symboliques

```bash
sudo chown -R www-data:www-data /var/www/citizen-forms/storage
sudo chown -R www-data:www-data /var/www/citizen-forms/bootstrap/cache
sudo chmod -R 775 /var/www/citizen-forms/storage
sudo chmod -R 775 /var/www/citizen-forms/bootstrap/cache

php artisan storage:link
```

### 3.7 Cache et optimisation

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🎯 Phase 4 : Installation de LimeSurvey

### 4.1 Télécharger et extraire LimeSurvey

```bash
cd /var/www
# Télécharger la dernière version (v5.x ou 6.x)
wget https://github.com/LimeSurvey/LimeSurvey/releases/download/5.4.27/limesurvey5.4.27.zip
unzip limesurvey5.4.27.zip
rm limesurvey5.4.27.zip

sudo chown -R www-data:www-data /var/www/limesurvey
sudo chmod -R 755 /var/www/limesurvey
sudo chmod -R 777 /var/www/limesurvey/upload
sudo chmod -R 777 /var/www/limesurvey/tmp
```

### 4.2 Configuration de LimeSurvey

```bash
cd /var/www/limesurvey
cp application/config/config-sample-db.php application/config/database.php
nano application/config/database.php
```

**Configurer pour MySQL:**

```php
'connectionString' => 'mysql:host=127.0.0.1;port=3306;dbname=limesurvey;',
'username' => 'lime_user',
'password' => 'SecurePassword456!',
'emulatePrepare' => true,
'charset' => 'utf8mb4',
'sslmode' => 'disable',
```

### 4.3 Configuration d'installation

```bash
# Créer le fichier de configuration temporaire pour installer
cd /var/www/limesurvey/application/config
cp config-sample-defaults.php config-defaults.php
nano config-defaults.php
```

Ajouter à la fin:

```php
'components' => array(
    'db' => array(
        'connectionString' => 'mysql:host=127.0.0.1;port=3306;dbname=limesurvey;',
        'username' => 'lime_user',
        'password' => 'SecurePassword456!',
        'emulatePrepare' => true,
        'charset' => 'utf8mb4',
    ),
),
```

### 4.4 Initialiser LimeSurvey via navigateur

Allez temporairement à `http://votre-ip/limesurvey/index.php` pour compléter l'installation via l'interface web.

Ou via CLI:

```bash
cd /var/www/limesurvey
php application/commands/console.php install admin AdminPassword123 admin@example.com
```

### 4.5 Sécuriser LimeSurvey après installation

```bash
# Supprimer le dossier d'installation
rm -rf /var/www/limesurvey/installer

# Restreindre les permissions
sudo chmod 755 /var/www/limesurvey/upload
sudo chmod 755 /var/www/limesurvey/tmp
```

---

## 🔐 Phase 5 : Configuration Nginx pour les deux apps

### 5.1 Créer le bloc pour Citizen Forms

```bash
sudo nano /etc/nginx/sites-available/citizen-forms
```

Contenu:

```nginx
server {
    listen 80;
    server_name forms.example.com www.forms.example.com;
    root /var/www/citizen-forms/public;

    index index.php;

    charset utf-8;

    # Logs
    access_log /var/log/nginx/citizen-forms.access.log;
    error_log /var/log/nginx/citizen-forms.error.log;

    # Redirect HTTP → HTTPS (après Let's Encrypt)
    # return 301 https://$server_name$request_uri;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";
}
```

### 5.2 Créer le bloc pour LimeSurvey

```bash
sudo nano /etc/nginx/sites-available/limesurvey
```

Contenu:

```nginx
server {
    listen 80;
    server_name survey.example.com www.survey.example.com;
    root /var/www/limesurvey;

    index index.php index.html;

    charset utf-8;

    # Logs
    access_log /var/log/nginx/limesurvey.access.log;
    error_log /var/log/nginx/limesurvey.error.log;

    # Redirect HTTP → HTTPS (après Let's Encrypt)
    # return 301 https://$server_name$request_uri;

    # Bloquer l'accès au dossier installer
    location ~ ^/installer/ {
        deny all;
        return 404;
    }

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
}
```

### 5.3 Activer les sites

```bash
sudo ln -s /etc/nginx/sites-available/citizen-forms /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/limesurvey /etc/nginx/sites-enabled/

# Désactiver le site par défaut
sudo rm /etc/nginx/sites-enabled/default

# Tester la configuration
sudo nginx -t

# Recharger Nginx
sudo systemctl reload nginx
```

---

## 🔒 Phase 6 : HTTPS avec Let's Encrypt (Certbot)

### 6.1 Installation de Certbot

```bash
sudo apt install -y certbot python3-certbot-nginx
```

### 6.2 Générer les certificats

```bash
# Pour Citizen Forms
sudo certbot --nginx -d forms.example.com -d www.forms.example.com

# Pour LimeSurvey
sudo certbot --nginx -d survey.example.com -d www.survey.example.com

# Renouvellement automatique
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer
```

---

## 🧪 Phase 7 : Tests et vérifications

### 7.1 Vérifier les services

```bash
# Vérifier que tout s'exécute
systemctl status php8.2-fpm
systemctl status mysql
systemctl status nginx

# Vérifier les logs
sudo tail -f /var/log/nginx/citizen-forms.error.log
sudo tail -f /var/log/nginx/limesurvey.error.log
```

### 7.2 Vérifier les permissions MySQL

```bash
sudo mysql -u root -p << 'EOF'
SELECT user, host FROM mysql.user WHERE user IN ('citizen_user', 'lime_user');
SHOW GRANTS FOR 'citizen_user'@'localhost';
SHOW GRANTS FOR 'lime_user'@'localhost';
EXIT;
EOF
```

### 7.3 Accéder aux applications

- **Citizen Forms** → `https://forms.example.com`
  - Admin: `admin@example.com` / `password`

- **LimeSurvey** → `https://survey.example.com`
  - Admin: `admin` / `AdminPassword123`

---

## 🛡️ Phase 8 : Sécurité et hardening

### 8.1 Pare-feu UFW

```bash
sudo apt install -y ufw
sudo ufw default deny incoming
sudo ufw default allow outgoing

# Autoriser SSH, HTTP, HTTPS
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

sudo ufw enable
sudo ufw status
```

### 8.2 Fail2Ban (protection contre brute-force)

```bash
sudo apt install -y fail2ban

# Créer config locale
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
sudo nano /etc/fail2ban/jail.local
```

Configurer:

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true

[nginx-http-auth]
enabled = true

[nginx-limit-req]
enabled = true
```

```bash
sudo systemctl restart fail2ban
sudo systemctl enable fail2ban
```

### 8.3 Désactiver les services inutiles

```bash
sudo systemctl disable snapd
sudo systemctl disable apport
```

---

## 📊 Phase 9 : Sauvegarde et maintenance

### 9.1 Sauvegarde automatique (cron)

```bash
sudo nano /usr/local/bin/backup-apps.sh
```

Contenu:

```bash
#!/bin/bash

BACKUP_DIR="/backups"
DATE=$(date +%Y%m%d_%H%M%S)

# Créer le dossier de sauvegarde
mkdir -p $BACKUP_DIR

# Sauvegarde des bases de données
sudo mysqldump -u root -p'ROOT_PASSWORD' --all-databases | gzip > $BACKUP_DIR/mysql_$DATE.sql.gz

# Sauvegarde des fichiers Citizen Forms
tar -czf $BACKUP_DIR/citizen-forms_$DATE.tar.gz /var/www/citizen-forms

# Sauvegarde des fichiers LimeSurvey
tar -czf $BACKUP_DIR/limesurvey_$DATE.tar.gz /var/www/limesurvey

# Supprimer les sauvegardes > 7 jours
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
sudo chmod +x /usr/local/bin/backup-apps.sh

# Planifier (tous les jours à 2h du matin)
sudo crontab -e
```

Ajouter:

```
0 2 * * * /usr/local/bin/backup-apps.sh >> /var/log/backup-apps.log 2>&1
```

### 9.2 Monitoring des disques

```bash
# Afficher l'utilisation du disque
df -h

# Afficher l'espace des bases de données
du -sh /var/www/citizen-forms /var/www/limesurvey /var/lib/mysql
```

---

## 🆘 Dépannage

| Problème | Solution |
|----------|----------|
| **502 Bad Gateway** | Vérifier `systemctl status php8.2-fpm`, vérifier les logs nginx |
| **Permission denied** | `sudo chown -R www-data:www-data /var/www/citizen-forms` |
| **Connection refused MySQL** | Vérifier `systemctl status mysql`, vérifier les credentials |
| **HTTPS non fonctionnel** | Vérifier les DNS, exécuter `sudo certbot renew --dry-run` |
| **Fichiers upload échouent** | Augmenter `upload_max_filesize` dans `/etc/php/8.2/fpm/php.ini` |

---

## 📝 Commandes utiles

```bash
# Redémarrer les services
sudo systemctl restart php8.2-fpm nginx mysql

# Vider les caches Laravel
php artisan cache:clear
php artisan config:clear

# Afficher les erreurs
sudo tail -f /var/log/nginx/error.log
php artisan log:tail

# Accès MySQL
mysql -u citizen_user -p citizen_forms

# Vérifier l'espace disque
df -h
```

---

## ✅ Checklist finale

- [ ] Ubuntu 22.04 LTS mis à jour
- [ ] PHP 8.2 + extensions installés
- [ ] MySQL 8 avec 2 bases + 2 utilisateurs
- [ ] Nginx configuré pour 2 domaines
- [ ] Citizen Forms: migrations et seeders exécutés
- [ ] LimeSurvey: installation complétée
- [ ] HTTPS Let's Encrypt activé
- [ ] UFW pare-feu activé
- [ ] Fail2Ban installé et configuré
- [ ] Sauvegardes cron configurées
- [ ] Accès aux 2 applications fonctionnels

---

**Besoin d'aide ?** Consultez:
- [Documentation Citizen Forms](./INSTALLATION.md)
- [Documentation LimeSurvey](https://manual.limesurvey.org/)
- [Documentation Nginx](https://nginx.org/en/docs/)
