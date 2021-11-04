## Project title
WP Fusion - Auto Retry

## Motivation
Sometime's a CRM's API can be temporarily down. This plugin detects API call failures due to 500, 503, and cURL timeouts. If an error is encountered, it schedules a cron task to retry the API call in 5 minutes.

It will only retry the call one time to prevent infinite looping. Failures and scheduled retries will be recorded in [the logs](https://wpfusion.com/documentation/getting-started/activity-logs/).

## Requires
[WP Fusion](http://wpfusion.com/) or [WP Fusion Lite](https://wordpress.org/plugins/wp-fusion-lite/)

## Changelog

### 1.0 - Initial release

### 1.1 - Dec 10th 2020
* Updated to detect 500 errors

### 1.2 - Nov 4th 2021
* Updated to detect 503 errors