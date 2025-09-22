#!/usr/bin/bash

chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

composer install --no-dev --prefer-dist

chown -R www-data:www-data /var/www/html/vendor \
    && chmod -R 775 /var/www/html/vendor

chown root:www-data /var/run/docker.sock