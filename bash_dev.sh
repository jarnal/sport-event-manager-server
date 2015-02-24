rm -Rf app/cache/prod/*
rm -Rf app/logs/*
php app/console asset:install --env=prod
php app/console assetic:dump
chown -R jarnal:www-data .
chmod -R 755 .
chmod -R 775 app/cache/
chmod -R 775 app/logs/
rm -Rf app/cache/prod/*
rm -Rf app/logs/*
