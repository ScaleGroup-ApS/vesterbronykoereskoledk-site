ARG PHP_VERSION=8.4
ARG FRANKENPHP_VERSION=1.11
ARG COMPOSER_VERSION=2.8

FROM composer:${COMPOSER_VERSION} AS vendor

FROM dunglas/frankenphp:${FRANKENPHP_VERSION}-php${PHP_VERSION}

ARG USER_ID=1000
ARG GROUP_ID=1000

ENV DEBIAN_FRONTEND=noninteractive \
    TERM=xterm-color \
    OCTANE_SERVER=frankenphp \
    USER=laravel \
    ROOT=/var/www/html \
    APP_ENV=production \
    COMPOSER_FUND=0 \
    COMPOSER_MAX_PARALLEL_HTTP=48

WORKDIR ${ROOT}

SHELL ["/bin/bash", "-eou", "pipefail", "-c"]

RUN apt-get update; \
    apt-get upgrade -yqq; \
    apt-get install -yqq --no-install-recommends --show-progress \
    curl \
    wget \
    unzip \
    ca-certificates \
    supervisor \
    && curl -fsSL https://bun.sh/install | BUN_INSTALL=/usr bash \
    && install-php-extensions \
    pcntl \
    mbstring \
    bcmath \
    pdo_mysql \
    opcache \
    zip \
    intl \
    gd \
    exif \
    sockets \
    && apt-get -y autoremove \
    && apt-get clean \
    && docker-php-source delete \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN arch="$(uname -m)" \
    && case "$arch" in \
    armhf) _cronic_fname='supercronic-linux-arm' ;; \
    aarch64) _cronic_fname='supercronic-linux-arm64' ;; \
    x86_64) _cronic_fname='supercronic-linux-amd64' ;; \
    x86) _cronic_fname='supercronic-linux-386' ;; \
    *) echo >&2 "error: unsupported architecture: $arch"; exit 1 ;; \
    esac \
    && wget -q "https://github.com/aptible/supercronic/releases/download/v0.2.38/${_cronic_fname}" \
    -O /usr/bin/supercronic \
    && chmod +x /usr/bin/supercronic \
    && mkdir -p /etc/supercronic \
    && echo "*/1 * * * * php ${ROOT}/artisan schedule:run --no-interaction" > /etc/supercronic/laravel

RUN userdel --remove --force www-data \
    && groupadd --force -g ${GROUP_ID} ${USER} \
    && useradd -ms /bin/bash --no-log-init --no-user-group -g ${GROUP_ID} -u ${USER_ID} ${USER}

RUN cp ${PHP_INI_DIR}/php.ini-production ${PHP_INI_DIR}/php.ini

COPY --link --from=vendor /usr/bin/composer /usr/bin/composer
COPY --link deployment/supervisord.conf /etc/
COPY --link deployment/supervisord.frankenphp.conf /etc/supervisor/conf.d/
COPY --link deployment/supervisord.services.conf /etc/supervisor/conf.d/
COPY --link deployment/start-container /usr/local/bin/start-container
COPY --link deployment/healthcheck /usr/local/bin/healthcheck
COPY --link deployment/php.ini ${PHP_INI_DIR}/conf.d/99-php.ini
COPY --link composer.json composer.lock ./

RUN composer install \
    --no-interaction \
    --no-ansi \
    --no-scripts \
    --no-progress

COPY --link package.json bun.lock* ./

RUN bun install

COPY --link . .

RUN mkdir -p storage/framework/{sessions,views,cache}
RUN mkdir -p storage/logs
RUN mkdir -p bootstrap/cache
RUN chown -R ${USER_ID}:${GROUP_ID} storage bootstrap/cache
RUN chmod +x /usr/local/bin/start-container /usr/local/bin/healthcheck

RUN composer dump-autoload --optimize

RUN bun run build

USER ${USER}

EXPOSE 8000
EXPOSE 2019

ENTRYPOINT ["start-container"]

HEALTHCHECK --start-period=5s --interval=2s --timeout=3s --retries=10 CMD healthcheck || exit 1
