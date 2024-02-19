# Speedtest Tracker

> [!IMPORTANT]
> `v0.16.0` includes a breaking change that requires user action for existing installs. Read the [release](https://github.com/alexjustesen/speedtest-tracker/releases/tag/v0.16.0) notes regarding the data migration.

[![Star History Chart](https://api.star-history.com/svg?repos=alexjustesen/speedtest-tracker&type=Date)](https://star-history.com/#alexjustesen/speedtest-tracker&Date)

## Introduction

Speedtest Tracker is a self-hosted internet performance tracking application that runs speedtest checks against Ookla's Speedtest service.

### Why might I use this?

The main use case for Speedtest Tracker is to build a history of your internet's performance so that you can be informed when you're not receiving your ISP's advertised rates.

### What about that other Speedtest Tracker?

As far as I can tell https://github.com/henrywhitaker3/Speedtest-Tracker was abandoned. This is meant to be an actively maintained replacement with an improved UI and feature set.

## Getting Started

Speedtest Tracker is containerized so you can run it anywhere you run your Docker containers. The [install](https://docs.speedtest-tracker.dev/getting-started/installation) documentation will get you up and running with using Docker or Docker Composer along with choosing a database (SQLite, MySQL/MariaDB or Postgresql).

### Quick Start

```dockerfile
docker run -d --name speedtest-tracker --restart unless-stopped \
    -p 8080:80 \
    -e PUID=1000 \
    -e PGID=1000 \
    -e DB_CONNECTION=sqlite \
    -v /path/to/data:/config \
    lscr.io/linuxserver/speedtest-tracker:latest
```

### FAQs and Features

[FAQs](https://docs.speedtest-tracker.dev/faqs) and a full list of planned and completed [features](https://docs.speedtest-tracker.dev/getting-started/features) can be found in the [documentation](https://docs.speedtest-tracker.dev).

## API

A robust API is planned for a later release but as of `v0.11.8` a legacy endpoint `/api/speedtest/latest` which is used by home lab dashboards like [Homepage](https://github.com/benphelps/homepage) and [Organizr](https://github.com/causefx/Organizr/tree/v2-master).

## Screenshots

![Dashboard](.github/screenshots/dashboard_screenshot.jpg)
**Dashboard**
