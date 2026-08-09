#!/bin/bash
# =====================================================
# DEPLOY SCRIPT - Laravel ke EC2
# EC2 IP: 52.20.146.63
# DB: hamdi / hamdi987
# =====================================================

set -e

echo "=== [1/7] Update & Install Dependencies ==="
sudo apt-get update -y
sudo apt-get upgrade -y
sudo apt-get install -y \
    git \
    curl \
    unzip \
    nginx \
    mysql-server \
    php8.3-fpm \
    php8.3-cli \
    php8.3-common \
    php8.3-mysql \
    php8.3-xml \
    php8.3-curl \
    php8.3-mbstring \
    php8.3-zip \
    php8.3-bcmath \
    php8.3-gd \
    php8.3-intl \
    php8.3-tokenizer \
    php8.3-fileinfo \
    composer \
    nodejs \
    npm

echo "=== [2/7] Setup MySQL: user=hamdi, pass=hamdi987, db=pzem_ta ==="
sudo mysql -e "CREATE DATABASE IF NOT EXISTS pzem_ta CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'hamdi'@'localhost' IDENTIFIED BY 'hamdi987';"
sudo mysql -e "GRANT ALL PRIVILEGES ON pzem_ta.* TO 'hamdi'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

echo "=== [3/7] Install phpMyAdmin ==="
sudo DEBIAN_FRONTEND=noninteractive apt-get install -y phpmyadmin
# Link phpMyAdmin ke direktori nginx
sudo ln -sf /usr/share/phpmyadmin /var/www/phpmyadmin

echo "=== [4/7] Clone Project Laravel dari GitHub ==="
sudo mkdir -p /var/www/laravel
sudo chown -R $USER:$USER /var/www/laravel
git clone https://github.com/hamdi123aja/project-listrik.git /var/www/laravel
sudo chown -R $USER:$USER /var/www/laravel

echo "=== [5/7] Setup Laravel ==="
cd /var/www/laravel
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Copy .env yang sudah dikonfigurasi (upload manual atau dari repo)
# pastikan .env sudah ada dengan konfigurasi production

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

# Fix permissions
sudo chown -R www-data:www-data /var/www/laravel
sudo chmod -R 755 /var/www/laravel
sudo chmod -R 775 /var/www/laravel/storage
sudo chmod -R 775 /var/www/laravel/bootstrap/cache

echo "=== [6/7] Setup Nginx ==="
sudo cp /var/www/laravel/nginx/laravel.conf /etc/nginx/sites-available/laravel
sudo ln -sf /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/laravel
sudo rm -f /etc/nginx/sites-enabled/default

# Tambah config phpMyAdmin ke Nginx
sudo bash -c 'cat > /etc/nginx/sites-available/phpmyadmin <<EOF
server {
    listen 8080;
    server_name 52.20.146.63;
    root /usr/share/phpmyadmin;
    index index.php;

    location / {
        try_files \$uri \$uri/ =404;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF'

sudo ln -sf /etc/nginx/sites-available/phpmyadmin /etc/nginx/sites-enabled/phpmyadmin

sudo nginx -t
sudo systemctl restart nginx
sudo systemctl enable nginx

echo "=== [7/7] Setup Service Queue (opsional) ==="
# Buat systemd service untuk queue worker
sudo bash -c 'cat > /etc/systemd/system/laravel-queue.service <<EOF
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/laravel/artisan queue:work --sleep=3 --tries=3 --max-time=3600
StandardOutput=append:/var/log/laravel-queue.log
StandardError=append:/var/log/laravel-queue.log

[Install]
WantedBy=multi-user.target
EOF'

sudo systemctl daemon-reload
sudo systemctl enable laravel-queue
sudo systemctl start laravel-queue

echo ""
echo "======================================"
echo " DEPLOY SELESAI!"
echo " Akses Laravel : http://52.20.146.63"
echo " Akses phpMyAdmin: http://52.20.146.63:8080"
echo "======================================"
