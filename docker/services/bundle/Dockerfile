FROM "php:fpm-alpine3.15" as base
RUN apk update
RUN apk add gcc autoconf ca-certificates
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

# Copy the Composer PHAR into the stage
COPY --from=composer /composer/composer.phar "${LOCAL_BIN_PATH}/composer"
RUN chmod +x "${LOCAL_BIN_PATH}/composer"

RUN apk add git
# Copy phpcov into the stage
COPY --from=phpcov /phpcov/phpcov.phar "${LOCAL_BIN_PATH}/phpcov"
RUN chmod +x "${LOCAL_BIN_PATH}/phpcov"

# Add Blackfire probe
RUN version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && architecture=$(uname -m) \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/alpine/$architecture/$version \
    && mkdir -p /tmp/blackfire \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire-*.so $(php -r "echo ini_get ('extension_dir');")/blackfire.so \
    && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8307\n" > $PHP_INI_DIR/conf.d/blackfire.ini \
    && rm -rf /tmp/blackfire /tmp/blackfire-probe.tar.gz

# Please note that the Blackfire Probe is dependent on the session module.
# If it isn't present in your install, you will need to enable it yourself.

WORKDIR /cache
VOLUME /cache

RUN chown root:root /cache
RUN chmod 0777 /cache

WORKDIR /cushon

RUN chown -R www-data:www-data /cushon
