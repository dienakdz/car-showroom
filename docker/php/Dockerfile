FROM php:8.3-fpm-alpine

RUN apk add --no-cache bash git curl zip unzip icu-dev oniguruma-dev libzip-dev mysql-client \
 && docker-php-ext-install -j$(nproc) pdo_mysql mbstring intl zip opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/zz-fpm-tuning.conf /usr/local/etc/php-fpm.d/zz-fpm-tuning.conf
COPY docker/php/zz-opcache.ini /usr/local/etc/php/conf.d/zz-opcache.ini

WORKDIR /var/www/html

RUN addgroup -g 1000 www && adduser -G www -g www -s /bin/sh -D -u 1000 www
USER www

CMD ["php-fpm"]
