#!/bin/bash

fail() {
    echo "Something went wrong, check /root/legacy-setup.log for more information!"
    exit 1
}

mkdir ~/legacy-setup-tmp
cd ~/legacy-setup-tmp
export COMPOSER_ALLOW_SUPERUSER=1

echo "-- Installing Dependencies"

# Adding php apt repository
sudo add-apt-repository -y ppa:ondrej/php > /root/legacy-setup.log 2>&1 || fail
sudo apt-get update -y >> /root/legacy-setup.log 2>&1 || fail

# Installing php and nginx
sudo apt install -y php7.4 php7.4-bcmath php7.4-bz2 php7.4-intl php7.4-gd php7.4-mbstring php7.4-mysql php7.4-common php7.4-zip php7.4-gmp php7.4-xml nginx php7.4-fpm >> /root/legacy-setup.log 2>&1 || fail

# Installing composer
curl -sS https://getcomposer.org/installer -o composer-setup.php >> /root/legacy-setup.log 2>&1 || fail
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer >> /root/legacy-setup.log 2>&1 || fail

echo "-- Cleaning up unused dependencies"

# Removing apache if its installed
sudo apt remove apache2-bin -y >> /root/legacy-setup.log 2>&1
sudo apt autoremove -y >> /root/legacy-setup.log 2>&1

echo "-- Configuring firewall"

# Restart nginx to make sure its running then add it to ufw
systemctl restart nginx 2>&1 || fail
sudo ufw allow 'Nginx Full' 2>&1 || fail

echo "-- Creating folder structure"

# Cleanup old data
rm -rf /var/www

# Create folder structure
mkdir /var/www
mkdir /var/www/socket-server
mkdir /var/www/docs
mkdir /var/www/ssl

echo "-- Creating self-signed certs for cloudflare"

# Create self-signed certificates for op-fw domains
openssl req -x509 -days 358000 -nodes -newkey rsa:2048 -subj '/C=XX/ST=XXXX/L=XXXX/O=XXXX/OU=XXXX/CN=opfw.net/emailAddress=info@wiese2.org' -addext 'subjectAltName = DNS:*.legacy-roleplay.com, DNS:*.opfw.net' -keyout key.pem -out cert.pem 2>&1 || fail

echo "-- Downloading and installing panel and socket"

# Download socket server
cd /var/www/socket-server

wget -q https://github.com/milan60/legacyrp-admin-panel-sockets/blob/master/admin-panel-sockets?raw=true -O admin-panel-sockets 2>&1 || fail
chmod +x admin-panel-sockets

rm display-map.json
wget -q https://raw.githubusercontent.com/milan60/legacyrp-admin-panel-sockets/master/display-map.json -O display-map.json 2>&1 || fail

rm vehicle-map.json
wget -q https://raw.githubusercontent.com/milan60/legacyrp-admin-panel-sockets/master/vehicle-map.json -O vehicle-map.json 2>&1 || fail

# Download panel
cd /var/www
git clone https://github.com/ExpDev07/legacy-rp-admin-v3.git 2>&1 || fail

# Install dependencies
cd legacy-rp-admin-v3
composer install 2>&1 || fail

# Fix file permissions
chown -R www-data .

echo "-- Cleanup"

# Remove temp folder that we created in the beginning
rm -rf ~/legacy-setup-tmp

echo "-- Installation complete"

echo "-- TODO:"
echo "  - Populate envs folder"
echo "  - Configure .env for socket-server"
echo "  - Download scripts for automation"
echo "  - Download scripts for automation"
echo "  - Configure nginx"
