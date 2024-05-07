# syntax=docker/dockerfile:1

FROM ghcr.io/linuxserver/baseimage-alpine-nginx:3.19

ARG BUILD_DATE
ARG VERSION
ARG SPEEDTEST_TRACKER_VERSION
LABEL build_version="Linuxserver.io version:- ${VERSION} Build-date:- ${BUILD_DATE}"
LABEL maintainer="alexjustesen"

ENV HOME=/config

RUN \
  apk add --no-cache \
    grep \
    php83-gd \
    php83-intl \
    php83-pdo_mysql \
    php83-pdo_pgsql \
    php83-pdo_sqlite \
    php83-pecl-redis \
    php83-tokenizer \
    php83-xmlreader \
    postgresql15-client \
    ssmtp && \
  apk add --no-cache --virtual=build-dependencies \
    npm && \
  echo "*** install speedtest-cli ***" && \
  if [ -z ${CLI_VERSION+x} ]; then \
    CLI_VERSION=$(curl -Ls https://packagecloud.io/ookla/speedtest-cli/debian/dists/bookworm/main/binary-amd64/Packages \
    | sed -n '/Package: speedtest/,/Homepage:/p' \
    | grep -oP 'Version: \K\S+' | cut -d. -f1-3); \
  fi && \
  curl -o \
    /tmp/speedtest-cli.tgz -L \
    "https://install.speedtest.net/app/cli/ookla-speedtest-${CLI_VERSION}-linux-x86_64.tgz" && \
  tar xzf \
    /tmp/speedtest-cli.tgz -C \
    /usr/bin && \
  echo "**** configure php-fpm to pass env vars ****" && \
  sed -E -i 's/^;?clear_env ?=.*$/clear_env = no/g' /etc/php83/php-fpm.d/www.conf && \
  grep -qxF 'clear_env = no' /etc/php83/php-fpm.d/www.conf || echo 'clear_env = no' >> /etc/php83/php-fpm.d/www.conf && \
  echo "env[PATH] = /usr/local/bin:/usr/bin:/bin" >> /etc/php83/php-fpm.conf && \
  echo "*** install speedtest-tracker ***" && \
  if [ -z ${SPEEDTEST_TRACKER_VERSION+x} ]; then \
    SPEEDTEST_TRACKER_VERSION=$(curl -sX GET "https://api.github.com/repos/alexjustesen/speedtest-tracker/releases/latest" \
    | awk '/tag_name/{print $4;exit}' FS='[""]'); \
  fi && \
  curl -o \
    /tmp/speedtest-tracker.tar.gz -L \
    "https://github.com/alexjustesen/speedtest-tracker/archive/${SPEEDTEST_TRACKER_VERSION}.tar.gz" && \
  mkdir -p /app/www && \
  tar xzf \
    /tmp/speedtest-tracker.tar.gz -C \
    /app/www/ --strip-components=1 && \
  cd /app/www && \
  composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-dev \
    --no-cache && \
  npm ci && npm run build && \
  echo "**** setup php opcache ****" && \
  { \
    echo 'opcache.enable_cli=1'; \
  } > /etc/php83/conf.d/opcache-recommended.ini; \
  { \
    echo 'post_max_size = 100M'; \
    echo 'upload_max_filesize = 100M'; \
    echo 'variables_order = EGPCS'; \
  } > /etc/php83/conf.d/php-misc.ini && \
  echo "**** cleanup ****" && \
  apk del --purge build-dependencies && \
  rm -rf \
    $HOME/.cache \
    $HOME/.npm \
    /app/www/node_modules \
    /tmp/*

COPY root/ /

VOLUME /config
