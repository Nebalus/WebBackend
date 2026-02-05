FROM php:8.3.13-fpm-alpine AS base
WORKDIR /var/www/
RUN chown -R www-data:www-data .
RUN apk update && apk add --no-cache php83-dev $PHPIZE_DEPS
RUN docker-php-ext-install pdo pdo_mysql
RUN pecl install redis && docker-php-ext-enable redis.so

####################################################################################

FROM composer:latest AS composerinstaller
WORKDIR /composer
COPY ./composer.json /composer
RUN composer install --ignore-platform-req=ext-redis

####################################################################################

FROM base AS development
COPY ./docker/php-fpm/development.ini $PHP_INI_DIR/conf.d/custom.php.ini
RUN apk add --update linux-headers
RUN pecl install xdebug-3.3.1 && docker-php-ext-enable xdebug

####################################################################################

FROM base AS production
COPY ./docker/php-fpm/production.ini $PHP_INI_DIR/conf.d/custom.php.ini
COPY ./src ./src
COPY ./public ./public
COPY ./templates ./templates
COPY --from=composerinstaller /composer/vendor ./vendor
