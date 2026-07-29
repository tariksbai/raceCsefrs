#!/bin/bash

#######################################
# Installation automatisée
# Citizen Forms Platform + LimeSurvey
# Ubuntu 22.04 LTS
#######################################

set -e  # Exit on error

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
DOMAIN_FORMS="forms.example.com"
DOMAIN_SURVEY="survey.example.com"
CITIZEN_DB_PASS="SecurePassword123!"
LIME_DB_PASS="SecurePassword456!"
LIME_ADMIN_PASS="AdminPassword123"

echo -e "${YELLOW}========================================${NC}"
echo -e "${YELLOW}Installation Citizen Forms + LimeSurvey${NC}"
echo -e "${YELLOW}========================================${NC}"

# Vérifier que c'est un utilisateur sudo
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Veuillez exécuter ce script avec sudo${NC}"
    exit 1
fi

# ====================
# Phase 1: Système
# ====================

echo -e "\n${GREEN}[1/8] Configuration système...${NC}"
apt update && apt upgrade -y
apt install -y curl wget git zip unzip htop vim software-properties-common

# ====================
# Phase 2: PHP + Extensions
# ====================

echo -e "\n${GREEN}[2/8] Installation de PHP 8.2...${NC}"
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y \
    php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-tokenizer \
    php8.2-fileinfo php8.2-openssl php8.2-pdo php8.2-gd php8.2-intl \
    php8.2-imap php8.2-ldap

# Configurer PHP-FPM
sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 50M/' /etc/php/8.2/fpm/php.ini
sed -i 's/post_max_size = 8M/post_max_size = 50M/' /etc/php/8.2/fpm/php.ini
systemctl restart php8.2-fpm

echo -e "${GREEN}✓ PHP 8.2 installé${NC}"

# ====================
# Phase 3: Composer
# ====================

echo -e "\n${GREEN}[3/8] Installation de Composer...${NC}"
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
echo -e "${GREEN}✓ Composer installé${NC}"

# ====================
# Phase 4: Node.js
# ====================

echo -e "\n${GREEN}[4/8] Installation de Node.js 18...${NC}"
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs
echo -e "${GREEN}✓ Node.js installé${NC}"

# ====================
# Phase 5: MySQL
# ====================

echo -e "\n${GREEN}[5/8] Installation de MySQL 8...${NC}"
DEBIAN_FRONTEND=noninteractive apt install -y mysql-server

systemctl start mysql
systemctl enable mysql

# Créer les bases et utilisateurs
mysql -u root << EOSQL
CREATE DATABASE IF NOT EXISTS citizen_forms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'citizen_user'@'localhost' IDENTIFIED BY '$CITIZEN_DB_PASS';
GRANT ALL PRIVILEGES ON citizen_forms.* TO 'citizen_user'@'localhost';

CREATE DATABASE IF NOT EXISTS limesurvey CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'lime_user'@'localhost' IDENTIFIED BY '$LIME_DB_PASS';
GRANT ALL PRIVILEGES ON limesurvey.* TO 'lime_user'@'localhost';

FLUSH PRIVILEGES;
EOSQL

echo -e "${GREEN}✓ MySQL 8 installé avec 2 bases${NC}"

# ====================
# Phase 6: Nginx
# ====================

echo -e "\n${GREEN}[6/8] Installation de Nginx...${NC}"
apt install -y nginx
systemctl start nginx
systemctl enable nginx

# Créer les vhosts
cat > /etc/nginx/sites-available/citizen-forms << 'EOF'
server {
    listen 80;
    server_name DOMAIN_FORMS www.DOMAIN_FORMS;
    root /var/www/citizen-forms/public;
    index index.php;

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

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
}
EOF

cat > /etc/nginx/sites-available/limesurvey << 'EOF'
server {
    listen 80;
    server_name DOMAIN_SURVEY www.DOMAIN_SURVEY;
    root /var/www/limesurvey;
    index index.php index.html;

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

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
}
EOF

# Remplacer les domaines
sed -i "s/DOMAIN_FORMS/$DOMAIN_FORMS/g" /etc/nginx/sites-available/citizen-forms
sed -i "s/DOMAIN_SURVEY/$DOMAIN_SURVEY/g" /etc/nginx/sites-available/limesurvey

# Activer les vhosts
ln -sf /etc/nginx/sites-available/citizen-forms /etc/nginx/sites-enabled/
ln -sf /etc/nginx/sites-available/limesurvey /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

nginx -t && systemctl reload nginx
echo -e "${GREEN}✓ Nginx configuré${NC}"

# ====================
# Phase 7: Citizen Forms
# ====================

echo -e "\n${GREEN}[7/8] Installation de Citizen Forms...${NC}"
mkdir -p /var/www
cd /var/www

if [ ! -d "citizen-forms" ]; then
    git clone https://github.com/tariksbai/raceCsefrs.git citizen-forms
fi

cd /var/www/citizen-forms
composer install --no-dev --optimize-autoloader

# Configuration .env
cp .env.example .env
sed -i "s/APP_ENV=local/APP_ENV=production/" .env
sed -i "s/APP_DEBUG=true/APP_DEBUG=false/" .env
sed -i "s/APP_URL=http:\/\/localhost/APP_URL=https:\/\/$DOMAIN_FORMS/" .env
sed -i "s/DB_USERNAME=root/DB_USERNAME=citizen_user/" .env
sed -i "s/DB_PASSWORD=/DB_PASSWORD=$CITIZEN_DB_PASS/" .env

# Migrations
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force

# Assets
npm install && npm run build

# Permissions
chown -R www-data:www-data /var/www/citizen-forms/storage
chown -R www-data:www-data /var/www/citizen-forms/bootstrap/cache
chmod -R 775 /var/www/citizen-forms/storage
chmod -R 775 /var/www/citizen-forms/bootstrap/cache

php artisan storage:link
php artisan config:cache
php artisan route:cache

echo -e "${GREEN}✓ Citizen Forms installée${NC}"

# ====================
# Phase 8: LimeSurvey
# ====================

echo -e "\n${GREEN}[8/8] Installation de LimeSurvey...${NC}"
cd /var/www

if [ ! -d "limesurvey" ]; then
    # Télécharger la dernière version stable (adapter le lien si nécessaire)
    wget https://github.com/LimeSurvey/LimeSurvey/releases/download/5.4.27/limesurvey5.4.27.zip
    unzip -q limesurvey5.4.27.zip
    rm limesurvey5.4.27.zip
fi

chown -R www-data:www-data /var/www/limesurvey
chmod -R 755 /var/www/limesurvey
chmod -R 777 /var/www/limesurvey/upload
chmod -R 777 /var/www/limesurvey/tmp

# Configuration LimeSurvey
cp /var/www/limesurvey/application/config/config-sample-db.php /var/www/limesurvey/application/config/database.php

cat > /var/www/limesurvey/application/config/database.php << EOSQL
<?php
return array(
    'connectionString' => 'mysql:host=127.0.0.1;port=3306;dbname=limesurvey;',
    'username' => 'lime_user',
    'password' => '$LIME_DB_PASS',
    'emulatePrepare' => true,
    'charset' => 'utf8mb4',
    'sslmode' => 'disable',
);
?>
EOSQL

echo -e "${GREEN}✓ LimeSurvey prêt à l'installation${NC}"

# ====================
# Résumé
# ====================

echo -e "\n${GREEN}========================================${NC}"
echo -e "${GREEN}Installation terminée avec succès !${NC}"
echo -e "${GREEN}========================================${NC}"

echo -e "\n${YELLOW}Informations d'accès:${NC}"
echo -e "  ${GREEN}Citizen Forms${NC}:"
echo -e "    URL: ${YELLOW}http://$DOMAIN_FORMS${NC}"
echo -e "    Email: admin@example.com"
echo -e "    Pass: password"
echo ""
echo -e "  ${GREEN}LimeSurvey${NC}:"
echo -e "    URL: ${YELLOW}http://$DOMAIN_SURVEY/index.php/admin${NC}"
echo -e "    Admin: admin"
echo -e "    Pass: $LIME_ADMIN_PASS"

echo -e "\n${YELLOW}Prochaines étapes:${NC}"
echo -e "  1. Configurer les DNS (A records):"
echo -e "     ${YELLOW}$DOMAIN_FORMS${NC} → $(hostname -I | awk '{print $1}')"
echo -e "     ${YELLOW}$DOMAIN_SURVEY${NC} → $(hostname -I | awk '{print $1}')"
echo ""
echo -e "  2. Installer HTTPS avec Let's Encrypt:"
echo -e "     ${YELLOW}sudo certbot --nginx${NC}"
echo ""
echo -e "  3. Renforcer la sécurité:"
echo -e "     ${YELLOW}sudo ufw enable${NC}"
echo -e "     ${YELLOW}sudo apt install fail2ban${NC}"

echo -e "\n${GREEN}Documentation complète: ./INSTALLATION_COMPLETE.md${NC}"
