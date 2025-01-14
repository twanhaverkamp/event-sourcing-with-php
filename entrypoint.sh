apk add --update --no-cache $PHPIZE_DEPS linux-headers gmp-dev
pecl install xdebug

docker-php-ext-install bcmath gmp
docker-php-ext-enable bcmath gmp xdebug

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
composer install

tail -f /dev/null
