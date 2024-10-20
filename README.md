# Mandrill Driver Plugin

[![MIT License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/wintercms/wn-drivermandrill-plugin/blob/main/LICENSE)

This plugin adds support for integrating Mandrill / Mailchimp into Winter CMS.

Supports:
- Configuring & using Mandrill / Mailchimp as a system mailer service.

## Installation

This plugin is available for installation via [Composer](http://getcomposer.org/).

```bash
composer require winter/wn-drivermandrill-plugin
```

After installing the plugin you will need to run the migrations and (if you are using a [public folder](https://wintercms.com/docs/develop/docs/setup/configuration#using-a-public-folder)) [republish your public directory](https://wintercms.com/docs/develop/docs/console/setup-maintenance#mirror-public-files).

```bash
php artisan migrate
```

## How to use this plugin

- Open an account with [Mailchimp](https://mailchimp.com/features/transactional-email-infrastructure/) and setup a secret key to use this plugin.
- Enter the secret on the Mail Configuration page after choosing the Mandrill Mail method.
