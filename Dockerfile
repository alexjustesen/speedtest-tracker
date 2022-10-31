FROM serversideup/php:8.2-fpm-nginx

# Add /config to allowed directory tree
ENV PHP_OPEN_BASEDIR=$WEBUSER_HOME:/config/:/dev/stdout:/tmp

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

WORKDIR /var/www/html

# Copy app
COPY --chown=webuser:webgroup . /var/www/html

# Install app dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev \
    && mkdir -p storage/logs \
    && php artisan optimize:clear \
    && chown -R webuser:webgroup /var/www/html \
    && crontab /etc/cron.d/scheduler

VOLUME /config
