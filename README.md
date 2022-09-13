## Speedtest Tracker

Welcome to Speedtest Tracker! Speedtest Tracker runs a speedtest check against Ookla's Speedtest service on a schedule.

This project replaces https://github.com/henrywhitaker3/Speedtest-Tracker as it looks like this project has been abandoned https://github.com/henrywhitaker3/Speedtest-Tracker/issues/1013.

### Roadmap
To suggest features please use the roadmap. You can also follow development progress there as well: https://speedtest-tracker-roadmap.alexjustesen.dev/


### Deployment

#### Docker w/ sqlite
```bash
docker run -itd --name speedtest-tracker \
    -p 8080:80 \
    -e "PHP_POOL_NAME=speedtest-tracker_php" \
    -e "DB_CONNECTION=sqlite" \
    -e "DB_DATABASE=/app/database.sqlite" \
    -v speedtest-tracker_app:/app \
    speedtest-tracker
```

#### Docker w/ MariaDB or MySQL
```bash
docker run -itd --name speedtest-tracker \
    -p 8080:80 \
    -e "PHP_POOL_NAME=speedtest-tracker_php" \
    -e "DB_CONNECTION=mysql" \
    -e "DB_HOST=mysql" \
    -e "DB_PORT=3306" \
    -e "DB_DATABASE=speedtest_tracker" \
    -e "DB_USERNAME=" \
    -e "DB_PASSWORD=" \
    speedtest-tracker
```

#### Docker Compose
```bash
# tbd...
```


### Build Docker Image
Want to build the image locally? Cool, just clone the repo and go right ahead...

```bash
docker build . -t speedtest-tracker
```

#### Runing the docker image
```bash
docker run -it -p 8080:80 \
    speedtest-tracker
```

### Development

Since this project uses Laravel as our framework of choice we can take advantage of [Laravel Sail](https://laravel.com/docs/9.x/sail) for a development environment.

#### Clone the repo

```bash
gh repo clone alexjustesen/speedtest-tracker \
    && cd speedtest-tracker \
    && cp .env.example .env
```

#### Install composer dependencies
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```

#### Start sail
```bash
./vendor/bin/sail up -d

# or, if you have the sail bash alias
sail up -d
```
