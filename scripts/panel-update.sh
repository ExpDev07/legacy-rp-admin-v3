cd opfw-admin

git stash
git pull

for directory in ./envs/*/; do
    [ -L "${d%/}" ] && continue

    cluster="$(basename -- $directory)"

    echo "Migrating $cluster";
    php artisan migrate --cluster=$cluster --force

    #echo "Rolling back $cluster";
    #php artisan migrate:rollback --cluster=$cluster --step=1 --force
done

rm update
