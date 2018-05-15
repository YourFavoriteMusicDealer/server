#!/bin/sh

cd /app
php composer.phar update
vendor/phalcon/devtools/phalcon.php migration run --config=./api/config/config.ini