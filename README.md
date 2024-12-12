# 🐇 Speedtest Tracker

Speedtest Tracker is a self-hosted application that monitors the performance and uptime of your internet connection.

[![Star History Chart](https://api.star-history.com/svg?repos=alexjustesen/speedtest-tracker&type=Date)](https://star-history.com/#alexjustesen/speedtest-tracker&Date)

### Why might I use this?

Speedtest Tracker's use case is to build a history of your internet's performance and uptime so that you can be informed when you're not receiving your ISP's advertised rates.

### What about that other Speedtest Tracker?

As far as I can tell https://github.com/henrywhitaker3/Speedtest-Tracker was abandoned. This is meant to be an actively maintained replacement with an improved UI and feature set.

## Getting Started

![Dashboard](.github/screenshots/dashboard.jpeg)

Speedtest Tracker is containerized so you can run it anywhere you run your containers. The image is built by LinuxServer.io, build information can be found [here](https://fleet.linuxserver.io/image?name=linuxserver/speedtest-tracker).

- [Installation](https://docs.speedtest-tracker.dev/getting-started/installation) will help you get up and running if you're deploying the Docker image or to a NAS platform like Synology and Unraid.
- [Configurations](https://docs.speedtest-tracker.dev/getting-started/environment-variables) are used to tailor Speedtest Tracker to your needs.
- [Notifications](https://docs.speedtest-tracker.dev/settings/notifications) keep you informed when issues happen.

### FAQs and Questions

- [Frequently Asked Questions](https://docs.speedtest-tracker.dev/help/faqs) is a collection of common questions.
- [Error Messages](https://docs.speedtest-tracker.dev/help/error-messages) are thrown when the application experiences a problem. These can help you resolve issues you might experience.
