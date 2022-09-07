cd legacy-rp-admin-v3
touch update

export COMPOSER_ALLOW_SUPERUSER=1

git stash
git pull
composer install

# Migrate all the clusters

echo "Migrating cluster 8"
php artisan migrate --cluster=c8

echo "Migrating cluster 10"
php artisan migrate --cluster=c10

echo "Migrating cluster 13"
php artisan migrate --cluster=c13

echo "Migrating cluster 15"
php artisan migrate --cluster=c15

# Done migrating

rm update
cd ..

chown -R www-data legacy-rp-admin-v3
