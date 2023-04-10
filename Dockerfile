FROM serversideup/php:8.1-fpm-nginx

# Add /config to allowed directory tree
ENV PHP_OPEN_BASEDIR=$WEBUSER_HOME:/config/:/dev/stdout:/tmp

# Enable mixed ssl mode so port 80 or 443 can be used
ENV SSL_MODE="mixed"

# Install addition packages and cron file
RUN apt-get update \
    && apt-get install -y --no-install-recommends cron gnupg php8.1-gd php8.1-pgsql \
    && echo "MAILTO=\"\"\n* * * * * webuser /usr/bin/php /var/www/html/artisan schedule:run" > /etc/cron.d/laravel \
    \
# Install Speedtest cli
    && curl -s https://packagecloud.io/install/repositories/ookla/speedtest-cli/script.deb.sh | bash \
    && apt-get install -y --no-install-recommends speedtest \
    \
# Clean up package lists
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Copy package configs
COPY --chmod=755 docker/deploy/etc/s6-overlay/ /etc/s6-overlay/

WORKDIR /var/www/html

# Copy app
COPY --chown=webuser:webgroup . /var/www/html

# Install app dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --no-cache \
    && mkdir -p storage/logs \
    && php artisan optimize:clear \
    && chown -R webuser:webgroup /var/www/html

VOLUME /config
