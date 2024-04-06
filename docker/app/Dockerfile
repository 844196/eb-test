FROM public.ecr.aws/composer/composer:2.7.2-bin as composer
FROM public.ecr.aws/docker/library/php:8.0.30-fpm-bullseye as base

RUN \
  useradd --shell /bin/bash --create-home --user-group appuser && \
  install -o appuser -g appuser -d /home/appuser/.config

RUN \
  apt-get update && \
  apt-get install -y --no-install-recommends \
    ca-certificates=* \
    libicu-dev=* \
    libonig-dev=* \
    libzip-dev=* \
    unzip=* \
    locales=* && \
  rm -rf /var/lib/apt/lists/*

RUN \
  pecl install redis-5.3.7 && \
  docker-php-ext-install -j"$(nproc)" intl pdo_mysql zip bcmath opcache && \
  docker-php-ext-enable redis

RUN \
  curl -sSfL -o awscliv2.zip https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip && \
  unzip awscliv2.zip && \
  ./aws/install && \
  rm -rf ./aws

COPY <<EOF /etc/locale.gen
en_US.UTF-8 UTF-8
ja_JP.UTF-8 UTF-8
EOF

RUN locale-gen

COPY ./docker/app/docker-php-do-not-expose.ini /usr/local/etc/php/conf.d/docker-php-do-not-expose.ini
COPY ./docker/app/docker-php-ext-opcache.ini /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini
COPY ./docker/app/zzz-docker.conf /usr/local/etc/php-fpm.d/zzz-docker.conf
COPY --from=composer /composer /usr/local/bin/composer

# SEE: https://www.php.net/manual/ja/opcache.installation.php
ENV PHP_OPCACHE_MEMORY_CONSUMPTION 128
ENV PHP_OPCACHE_INTERNED_STRINGS_BUFFER 8
ENV PHP_OPCACHE_MAX_ACCELERATED_FILES 4000
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS On
ENV PHP_OPCACHE_REVALIDATE_FREQ 60

ENV PHP_FCGI_LISTEN 9000
ENV PHP_FCGI_PM_MAX_CHILDREN 16
ENV PHP_FCGI_PM_START_SERVERS 4
ENV PHP_FCGI_PM_MIN_SPARE_SERVERS 4
ENV PHP_FCGI_PM_MAX_SPARE_SERVERS 8
ENV PHP_FCGI_PM_MAX_REQUESTS 1024
ENV COMPOSER_MEMORY_LIMIT -1

# ------------------------------------------------------------------------------

FROM base as builder

COPY --chown=appuser:appuser . /opt/app/

# ------------------------------------------------------------------------------

FROM base as live

# コードが変化しないため、キャッシュのタイムスタンプ検証を行わない
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS Off

ARG PHP_INI_ENVIRONMENT=production
RUN cp "${PHP_INI_DIR}/php.ini-${PHP_INI_ENVIRONMENT}" "${PHP_INI_DIR}/php.ini"

COPY --chown=appuser:appuser --chmod=0755 ./docker/app/docker-entrypoint.live.sh /docker-entrypoint.live.sh

COPY --chown=appuser:appuser --from=builder /opt/app /opt/app

USER appuser
ENTRYPOINT ["/docker-entrypoint.live.sh"]
CMD ["php-fpm"]
WORKDIR /opt/app
