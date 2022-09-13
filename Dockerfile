FROM serversideup/php:8.1-fpm-nginx

# Install addition packages
RUN apt-get update && apt-get install -y \
    cron \
    php8.1-bcmath \
    php8.1-pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install Speedtest cli
RUN curl -s https://packagecloud.io/install/repositories/ookla/speedtest-cli/script.deb.sh | bash \
    && apt-get install -y speedtest

# Copy package configs
COPY docker/deploy/cron/scheduler /etc/cron.d/scheduler
COPY docker/deploy/etc/services.d/ /etc/services.d/

# Copy app
COPY . /var/www/html
COPY .env.docker .env

# Install app dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev \
    && mkdir -p /app \
    && mkdir -p storage/logs \
    && php artisan optimize:clear \
    && chown -R webuser:webgroup /var/www/html \
    && rm -rf /etc/cont-init.d/50-laravel-automations \
    && chmod 0644 /etc/cron.d/scheduler \
    && crontab /etc/cron.d/scheduler \
    && cp docker/deploy/entrypoint.sh /entrypoint \
    && chmod +x /entrypoint

ENTRYPOINT ["/entrypoint"]
