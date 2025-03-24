ARG PHP_VERSION
ARG APP_ENV

#############################
###        PHP-FPM        ###
#############################
FROM php:${PHP_VERSION}-fpm-alpine AS php-base

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions bcmath ctype iconv intl opcache pdo_mysql @composer

ARG PHP_TIMEZONE=${PHP_TIMEZONE:-UTC}
RUN echo "date.timezone = ${PHP_TIMEZONE}" > $PHP_INI_DIR/conf.d/tzone.ini

WORKDIR /app
COPY composer.json composer.lock ./

RUN if [ "$APP_ENV" = "prod" ] ;  \
    then ln -sf "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" ; \
    else ln -sf "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" ; fi
RUN if [ "$APP_ENV" = "prod" ] ;  \
    then composer install --prefer-dist --no-progress --no-interaction --optimize-autoloader --no-scripts --no-dev ; \
    else composer install --prefer-dist --no-progress --no-interaction --optimize-autoloader --no-scripts ; fi
RUN if [ "$APP_ENV" = "prod" ] ; then composer dump-autoload --classmap-authoritative --no-dev ; fi


#############################
###        ASSETS         ###
#############################
FROM node AS yarn-encore

ARG APP_ENV
ENV APP_ENV=${APP_ENV}

WORKDIR /app
COPY ./assets ./assets
COPY --from=php-base /app/vendor ./vendor
COPY package.json tsconfig.json webpack.config.js yarn.lock postcss.config.js composer.json composer.lock ./

RUN yarn install --force --frozen-lockfile
RUN yarn encore ${APP_ENV}


#############################
###        PHP-FPM        ###
#############################
FROM php-base

ARG APP_ENV
ENV APP_ENV=${APP_ENV}
ARG APP_SECRET
ENV APP_SECRET=${APP_SECRET}

USER root
RUN apk add --update --no-cache \
    supervisor nginx acl fcgi file gettext gnu-libiconv

COPY docker/php-fpm/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint && sync

COPY docker/php-fpm/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck
HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

# Setup php/php-fpm + nginx config
COPY docker/php-fpm/php.ini $PHP_INI_DIR/conf.d/symfony.ini
COPY docker/php-fpm/php-fpm.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY docker/nginx/ /etc/nginx/http.d/
COPY docker/php-fpm/supervisord.conf /etc/supervisord.conf

COPY . .
COPY --from=yarn-encore /app/public/build ./public/build
RUN mkdir -p var/cache var/log public/uploads && chmod -R 777 var/cache var/log public/uploads && sync

RUN sed -i -e "s/APP_SECRET=.*/APP_SECRET=${APP_SECRET}/g" ./.env

ENTRYPOINT ["docker-entrypoint"]
CMD ["supervisord", "-n", "-c", "/etc/supervisord.conf"]
