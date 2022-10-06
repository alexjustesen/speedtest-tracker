FROM serversideup/php:beta-8.1-fpm-nginx

ENV PHP_POOL_NAME=speedtest-tracker_php
ENV PHP_POST_MAX_SIZE=1G
ENV PHP_UPLOAD_MAX_FILE_SIZE=1G

# Install addition packages
RUN apt-get update && apt-get install -y \
    cron \
    gnupg \
    php8.1-pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install Speedtest cli
RUN curl -s https://packagecloud.io/install/repositories/ookla/speedtest-cli/script.deb.sh | bash \
    && apt-get install -y speedtest

# Copy package configs
COPY --chmod=644 docker/deploy/cron/scheduler /etc/cron.d/scheduler
COPY --chmod=755 docker/deploy/etc/s6-overlay/ /etc/s6-overlay/

# Copy app
COPY --chown=webuser:webgroup . /var/www/html
COPY .env.docker .env

# Install app dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev \
    && mkdir -p /app \
    && mkdir -p storage/logs \
    && php artisan optimize:clear \
    && chown -R webuser:webgroup /var/www/html \
    && crontab /etc/cron.d/scheduler
