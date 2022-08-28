cd legacy-rp-admin-v3
touch update

export COMPOSER_ALLOW_SUPERUSER=1

git stash
git pull
composer install

# Migrate all the clusters

echo "Migrating cluster 2"
php artisan migrate --cluster=c2

echo "Migrating cluster 3"
php artisan migrate --cluster=c3

echo "Migrating cluster 4"
php artisan migrate --cluster=c4

echo "Migrating cluster 5"
php artisan migrate --cluster=c5

echo "Migrating cluster 6"
php artisan migrate --cluster=c6

echo "Migrating cluster 16"
php artisan migrate --cluster=c16

# Done migrating

rm update
cd ..

chown -R www-data legacy-rp-admin-v3
