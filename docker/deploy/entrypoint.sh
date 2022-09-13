#!/usr/bin/env sh

if [ $# -gt 0 ];then
    # If we passed a command, run it as root
    exec "$@"
else
    echo ""
    echo ""
    echo "🐇  Configuring Speedtest Tracker..."

    if [ ${DB_CONNECTION:="sqlite"} == "sqlite" ]; then
        # Check for database
        if [ ! -f /app/database.sqlite ]; then
            echo "🙄  Database file not found, creating..."

            touch /app/database.sqlite
        else
            echo "✅  Database exists"
        fi
    fi

    # Check for config yaml file
    if [ ! -f /app/config.yml ]; then
        echo "🙄  Config file not found, creating..."
        cp /var/www/html/config.example.yml /app/config.yml
    else
        echo "✅  Config file exists"
    fi

    # Check for app key
    if grep -E "APP_KEY=[0-9A-Za-z:+\/=]{1,}" /var/www/html/.env > /dev/null; then
        echo "✅  App key exists"
    else
        echo "⏳  Generating app key..."
        php /var/www/html/artisan key:generate --no-ansi -q
    fi

    # Link storage
    echo "📦  Linking storage..."
    php /var/www/html/artisan storage:link --no-ansi -q

    # Build cache
    echo "💰  Building the cache..."
    php /var/www/html/artisan config:cache --no-ansi -q
    php /var/www/html/artisan route:cache --no-ansi -q

    # Migrate database
    echo "🚛  Migrating the database..."
    php /var/www/html/artisan migrate --force --no-ansi -q

    # Fix permissions again, just in case
    echo "🔑  Fixing permissions..."
    chown -R webuser:webgroup /var/www/html

    # App install done, show a message
    echo "✅  All set, starting Speedtest Tracker container..."
    echo ""
    echo ""

    exec /init
fi
