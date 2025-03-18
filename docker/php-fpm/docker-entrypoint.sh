#!/bin/sh
set -e

# prefix 'php-fpm' if first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
  set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ] || [ "$1" = 'supervisord' ]; then

  rm -f .env.local.php
  if [ "$APP_ENV" = 'prod' ]; then
    ln -sf "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
  else
    ln -sf "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
  fi

  ATTEMPTS_MAX_TO_REACH_DATABASE=300 # 5min
  ATTEMPTS_LEFT_TO_REACH_DATABASE=$ATTEMPTS_MAX_TO_REACH_DATABASE

  until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || DATABASE_ERROR=$(bin/console doctrine:query:sql "SELECT 1" 2>&1); do
    echo "[ENTRYPOINT] [$ATTEMPTS_LEFT_TO_REACH_DATABASE/$ATTEMPTS_MAX_TO_REACH_DATABASE] Waiting for db to be ready..."

    if [ $? -eq 255 ]; then
      # If the Doctrine command exits with 255, an unrecoverable error occurred
      ATTEMPTS_LEFT_TO_REACH_DATABASE=0
      break
    fi
    sleep 1
    ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))
  done

  if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
    echo "[ENTRYPOINT] The database is not up or not reachable:"
    echo "$DATABASE_ERROR"
    exit 1
  else
    echo "[ENTRYPOINT] The db is now ready and reachable"
  fi

  if [ "$APP_ENV" = 'prod' ]; then
    composer run-script post-install-cmd --no-dev
  else
    composer run-script post-install-cmd
  fi

  composer symfony:dump-env "$APP_ENV"

  # run doctrine migrations if required
  if ls -A migrations/*.php >/dev/null 2>&1; then
    bin/console doctrine:migrations:migrate --allow-no-migration --no-interaction
  fi

  # warmup cache
  bin/console cache:clear --no-interaction --no-warmup
  bin/console cache:warmup --no-interaction
fi

exec docker-php-entrypoint "$@"
