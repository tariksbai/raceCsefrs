# Maintenance et Opérations - Citizen Forms + LimeSurvey

Commandes essentielles pour gérer les deux applications en production.

---

## 🔍 Supervision et Monitoring

### État des services

```bash
# Vérifier tous les services
systemctl status nginx mysql php8.2-fpm

# Redémarrer les services
sudo systemctl restart nginx
sudo systemctl restart mysql
sudo systemctl restart php8.2-fpm

# Restart complet
sudo systemctl restart nginx mysql php8.2-fpm
```

### Logs en temps réel

```bash
# Logs Nginx (Citizen Forms)
sudo tail -f /var/log/nginx/citizen-forms.error.log
sudo tail -f /var/log/nginx/citizen-forms.access.log

# Logs Nginx (LimeSurvey)
sudo tail -f /var/log/nginx/limesurvey.error.log
sudo tail -f /var/log/nginx/limesurvey.access.log

# Logs Laravel
cd /var/www/citizen-forms
php artisan log:tail

# Logs système
sudo tail -f /var/log/syslog
```

### Monitoring disque et mémoire

```bash
# Utilisation disque
df -h
du -sh /var/www/citizen-forms /var/www/limesurvey /var/lib/mysql

# Processus PHP
ps aux | grep -E 'php|nginx'

# Utilisation mémoire
free -h
top
```

---

## 🔄 Mises à jour

### Mettre à jour le système Ubuntu

```bash
sudo apt update
sudo apt upgrade
sudo apt autoremove
```

### Mettre à jour Citizen Forms

```bash
cd /var/www/citizen-forms

# Récupérer les mises à jour
git pull origin main

# Installer dépendances PHP
composer install --no-dev --optimize-autoloader

# Exécuter migrations si nécessaire
php artisan migrate --force

# Compiler assets
npm install
npm run build

# Nettoyer les caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Réapplying optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Redémarrer PHP-FPM
sudo systemctl restart php8.2-fpm
```

### Mettre à jour LimeSurvey

```bash
# Vérifier la version actuelle
head -20 /var/www/limesurvey/application/config/config.php

# Sauvegarder avant
sudo tar -czf /backups/limesurvey_$(date +%Y%m%d).tar.gz /var/www/limesurvey

# Télécharger nouvelle version
cd /tmp
wget https://github.com/LimeSurvey/LimeSurvey/releases/download/[VERSION]/limesurvey[VERSION].zip
unzip limesurvey[VERSION].zip

# Sauvegarder config
cp /var/www/limesurvey/application/config/database.php /tmp/database.php

# Remplacer
sudo rm -rf /var/www/limesurvey/*
sudo cp -r /tmp/limesurvey/* /var/www/limesurvey/

# Restaurer config
sudo cp /tmp/database.php /var/www/limesurvey/application/config/

# Permissions
sudo chown -R www-data:www-data /var/www/limesurvey
sudo chmod -R 777 /var/www/limesurvey/upload /var/www/limesurvey/tmp

# Nettoyer installation
sudo rm -rf /var/www/limesurvey/installer
```

---

## 💾 Sauvegarde et Restauration

### Sauvegarde manuelle

```bash
# Créer dossier backup
mkdir -p /backups
cd /backups

# Sauvegarder les bases de données
sudo mysqldump --all-databases -u root -p | gzip > mysql_backup_$(date +%Y%m%d_%H%M%S).sql.gz

# Ou par base
sudo mysqldump -u root -p citizen_forms | gzip > citizen_forms_$(date +%Y%m%d_%H%M%S).sql.gz
sudo mysqldump -u root -p limesurvey | gzip > limesurvey_$(date +%Y%m%d_%H%M%S).sql.gz

# Sauvegarder les applications
tar -czf citizen-forms_$(date +%Y%m%d_%H%M%S).tar.gz /var/www/citizen-forms
tar -czf limesurvey_$(date +%Y%m%d_%H%M%S).tar.gz /var/www/limesurvey

# Lister les sauvegardes
ls -lh /backups/
```

### Sauvegarde automatique (cron)

```bash
# Éditer crontab root
sudo crontab -e

# Ajouter (sauvegarder chaque jour à 2h du matin)
0 2 * * * /usr/local/bin/backup-server.sh >> /var/log/backup.log 2>&1

# Ou deux fois par jour
0 2 * * * /usr/local/bin/backup-server.sh >> /var/log/backup.log 2>&1
0 14 * * * /usr/local/bin/backup-server.sh >> /var/log/backup.log 2>&1
```

Créer `/usr/local/bin/backup-server.sh`:

```bash
#!/bin/bash
BACKUP_DIR="/backups"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Bases de données
sudo mysqldump --all-databases -u root -p'ROOT_PASSWORD' | gzip > $BACKUP_DIR/mysql_$DATE.sql.gz

# Applications
tar -czf $BACKUP_DIR/citizen-forms_$DATE.tar.gz /var/www/citizen-forms 2>/dev/null
tar -czf $BACKUP_DIR/limesurvey_$DATE.tar.gz /var/www/limesurvey 2>/dev/null

# Garder que 30 jours
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup completed: $DATE"
```

### Restaurer une sauvegarde

```bash
# Restaurer base de données
sudo mysql -u root -p < /backups/mysql_backup_DATE.sql

# Ou une base spécifique
sudo mysql -u root -p citizen_forms < /backups/citizen_forms_DATE.sql

# Restaurer les fichiers
sudo tar -xzf /backups/citizen-forms_DATE.tar.gz -C /
sudo tar -xzf /backups/limesurvey_DATE.tar.gz -C /

# Restaurer permissions
sudo chown -R www-data:www-data /var/www/citizen-forms /var/www/limesurvey
```

---

## 🧪 Maintenance de Citizen Forms

### Clear caches

```bash
cd /var/www/citizen-forms

php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Database maintenance

```bash
cd /var/www/citizen-forms

# Vérifier les migrations pending
php artisan migrate:status

# Lancer migrations
php artisan migrate

# Rollback
php artisan migrate:rollback
```

### Permissions utilisateurs

```bash
cd /var/www/citizen-forms

# Créer admin
php artisan tinker
> User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password'), 'status' => 'active', 'user_type' => 'admin'])

# Ou ajouter un rôle existant
> $user = User::find(1)
> $user->roles()->attach(1)  // 1 = ID du rôle admin
```

### Cleanup old data

```bash
cd /var/www/citizen-forms

# Supprimer les logs d'accès anciens
mysql -u citizen_user -p citizen_forms << 'EOF'
DELETE FROM form_access_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
EOF

# Optimiser les tables
mysql -u citizen_user -p citizen_forms << 'EOF'
OPTIMIZE TABLE form_access_logs;
OPTIMIZE TABLE internal_form_responses;
EOF
```

---

## 🧪 Maintenance de LimeSurvey

### Vérifier la santé

```bash
cd /var/www/limesurvey

# Vérifier les dossiers writable
ls -la application/config/ | grep database.php
ls -la upload/
ls -la tmp/

# Vérifier les permissions
sudo chown -R www-data:www-data /var/www/limesurvey
```

### Cleanup

```bash
cd /var/www/limesurvey

# Nettoyer les fichiers temporaires
rm -rf tmp/*

# Vider le cache
rm -rf application/cache/*
```

---

## 🔐 Sécurité

### Activer UFW (Firewall)

```bash
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
sudo ufw status
```

### Fail2Ban (Anti-brute force)

```bash
# Installer
sudo apt install -y fail2ban

# Configurer
sudo nano /etc/fail2ban/jail.local
```

Configuration:

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true
```

```bash
sudo systemctl restart fail2ban
```

### Certificat SSL (Let's Encrypt)

```bash
# Installer Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtenir certificats
sudo certbot --nginx -d forms.example.com -d www.forms.example.com
sudo certbot --nginx -d survey.example.com -d www.survey.example.com

# Auto-renouvellement
sudo systemctl enable certbot.timer
sudo certbot renew --dry-run
```

### Vérifier certificats

```bash
# Voir les certificats
sudo certbot certificates

# Renouveler manuellement
sudo certbot renew

# Logs
sudo tail -f /var/log/letsencrypt/letsencrypt.log
```

---

## 📊 Performance et Optimization

### Optimiser PHP-FPM

```bash
# Éditer configuration
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
```

Configuration importante:

```ini
; Nombre maximum de processus
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20

; Augmenter limites
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
```

```bash
sudo systemctl restart php8.2-fpm
```

### Optimiser MySQL

```bash
# Éditer configuration
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Ajouter après `[mysqld]`:

```ini
# Buffer pool (50-70% de la RAM disponible)
innodb_buffer_pool_size = 2G

# Log binaire
expire_logs_days = 7

# Slow queries
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 2
```

```bash
sudo systemctl restart mysql
```

### Vérifier les slow queries

```bash
sudo tail -f /var/log/mysql/slow.log
```

---

## 🔧 Troubleshooting

### Application inaccessible

```bash
# Vérifier Nginx
sudo nginx -t
sudo systemctl status nginx

# Vérifier PHP-FPM
sudo systemctl status php8.2-fpm
ps aux | grep php

# Vérifier DNS
nslookup forms.example.com
```

### Erreur 502 Bad Gateway

```bash
# Vérifier socket PHP
ls -l /var/run/php/php8.2-fpm.sock

# Vérifier connexion PHP
sudo systemctl restart php8.2-fpm

# Logs
sudo tail -f /var/log/nginx/error.log
```

### Permission denied

```bash
# Vérifier permissions
ls -la /var/www/citizen-forms/storage
ls -la /var/www/citizen-forms/bootstrap/cache

# Fixer
sudo chown -R www-data:www-data /var/www/citizen-forms/storage
sudo chown -R www-data:www-data /var/www/citizen-forms/bootstrap/cache
sudo chmod -R 775 /var/www/citizen-forms/storage
```

### Database connection error

```bash
# Vérifier MySQL
sudo systemctl status mysql

# Tester connection
mysql -u citizen_user -p -h 127.0.0.1 citizen_forms -e "SELECT 1"
mysql -u lime_user -p -h 127.0.0.1 limesurvey -e "SELECT 1"

# Vérifier credentials dans .env
cat /var/www/citizen-forms/.env | grep DB_
```

---

## 📝 Checklist maintenance hebdomadaire

- [ ] Vérifier l'utilisation disque (`df -h`)
- [ ] Vérifier les logs d'erreur Nginx
- [ ] Vérifier l'espace base de données (`du -sh /var/lib/mysql`)
- [ ] Mettre à jour le système (`apt update && apt upgrade`)
- [ ] Vérifier les certificats SSL (`certbot certificates`)
- [ ] Backup manuelle (`mysqldump + tar`)
- [ ] Vérifier les services en cours d'exécution (`systemctl status`)

---

## 📝 Checklist maintenance mensuelle

- [ ] Optimiser les tables MySQL
- [ ] Nettoyer les logs anciens
- [ ] Vérifier les permissions des fichiers
- [ ] Analyser les slow queries
- [ ] Mettre à jour les packages PHP/Node
- [ ] Vérifier l'authentification SSH
- [ ] Revoir les certificats SSL (90+ jours avant expiration)

---

## 🆘 Support et Ressources

- [Documentation Citizen Forms](./INSTALLATION.md)
- [Documentation LimeSurvey](https://manual.limesurvey.org/)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)

---

**Dernière mise à jour**: 2026-03-21
