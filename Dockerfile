FROM serversideup/php:8.1-fpm-nginx

# Install addition packages
RUN apt-get update && apt-get install -y \
    cron \
    supervisor \
    php8.1-bcmath \
    php8.1-pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* \
    && rm -f /etc/cont-init.d/50-laravel-automations

# Install Speedtest cli
RUN curl -s https://packagecloud.io/install/repositories/ookla/speedtest-cli/script.deb.sh | bash \
    && apt-get install -y speedtest

# Copy package configs
COPY docker/deploy/cron/scheduler /etc/cron.d/scheduler
COPY docker/deploy/entrypoint /usr/local/bin/entrypoint
COPY docker/deploy/etc/cont-init.d/ /etc/cont-init.d/
COPY docker/deploy/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN chmod 0644 /etc/cron.d/scheduler \
    && crontab /etc/cron.d/scheduler \
    && chmod +x /usr/local/bin/entrypoint

# Copy app
COPY . /var/www/html
COPY .env.docker .env

# Install app dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev \
    && chown -R 9999:9999 /var/www/html

ENTRYPOINT ["entrypoint"]
