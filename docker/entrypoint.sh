#!/bin/bash

cd /var/www/html/

export NVM_DIR="$([ -z "${XDG_CONFIG_HOME-}" ] && printf %s "${HOME}/.nvm" || printf %s "${XDG_CONFIG_HOME}/nvm")"
    [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" # This loads nvm

chown -R $USER:www-data .

find . -type f -exec chmod 664 {} \;   

find . -type d -exec chmod 775 {} \;

composer install --no-interaction

npm install -y

npm run build

chgrp -R www-data storage bootstrap/cache

chmod -R ug+rwx storage bootstrap/cache

php artisan key:generate

php artisan optimize

exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf