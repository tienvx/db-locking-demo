#!/bin/sh

/app/resources/wait-for-it.sh -t 0 db:3306

php /app/bin/console doctrine:schema:create
php -S 0.0.0.0:80 -t /app/public
