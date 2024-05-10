#!/bin/sh

# Inicializar o cron
crond -f &

# Iniciar o PHP-FPM
php-fpm -y /usr/local/etc/php-fpm.conf -R
