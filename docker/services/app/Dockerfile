ARG PHP_IMAGE_APP_VERSION="fpm-alpine3.15"

FROM "php:${PHP_IMAGE_APP_VERSION}" as base
RUN apk update
RUN apk add gcc autoconf
RUN apk add --no-cache --virtual .phpize-deps ${PHPIZE_DEPS}
RUN pecl install -o -f ds pcov pcntl posix
RUN docker-php-ext-enable opcache ds pcov

FROM base AS composer
WORKDIR /composer
ADD docker/shared/composer.sh ./
RUN chmod +x ./composer.sh
RUN ./composer.sh

FROM base AS phpcov
WORKDIR /phpcov
RUN apk add --update wget
RUN wget 'https://phar.phpunit.de/phpcov.phar'

FROM base as cushon
ARG COMPOSER_PATH=/cushon
ARG LOCAL_BIN_PATH=/usr/local/bin
ENV APP_ENV=prod

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=composer /composer/composer.phar "${LOCAL_BIN_PATH}/composer"
RUN chmod +x "${LOCAL_BIN_PATH}/composer"

RUN apk add git

COPY --from=phpcov /phpcov/phpcov.phar "${LOCAL_BIN_PATH}/phpcov"
RUN chmod +x "${LOCAL_BIN_PATH}/phpcov"

WORKDIR /cache
VOLUME /cache

RUN chown root:root /cache
RUN chmod 0777 /cache

WORKDIR /cushon

RUN chown -R www-data:www-data /cushon
