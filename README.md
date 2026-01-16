# PHP Extras

Collection of PHP pre-execution script helpers. These helpers leverage the PHP `auto_prepend_file` configuration directive to modify the runtime environment before the main application script is executed. They are primarily used by [docker-php-apache](https://github.com/panubo/docker-php-apache) and [docker-apache-mvh](https://github.com/panubo/docker-apache-mvh) containers. While currently specific, we aim to make them more generic in the future and welcome contributions.

## Helpers

### ProxyHelper

Adjusts `$_SERVER` variables to correctly reflect the client's IP and protocol when the application is behind a reverse proxy.

- Sets `$_SERVER['REMOTE_ADDR']` from `HTTP_X_REAL_IP`.
- Sets `$_SERVER['HTTPS'] = 'on'`, `$_SERVER['REQUEST_SCHEME'] = 'https'` and `$_SERVER['protossl'] = 's'` if `HTTP_X_FORWARDED_PROTO` is `https`.

### SSLHelper

A focused helper for SSL termination. If you only need to handle the SSL part and not the remote address, use this helper.

- Sets `$_SERVER['HTTPS'] = 'on'`, `$_SERVER['REQUEST_SCHEME'] = 'https'` and `$_SERVER['protossl'] = 's'` if `HTTP_X_FORWARDED_PROTO` is `https`.

### WordPressHelper

Configures WordPress using environment variables. This is useful for containerized WordPress deployments. It can set common configuration values like database credentials and security keys automatically.

The following environment variables can be used:
- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASSWORD`
- `DB_CHARSET`
- `WP_DEBUG`
- `WP_HOME`
- `WP_SITEURL`
- `AUTH_KEY`
- `SECURE_AUTH_KEY`
- `LOGGED_IN_KEY`
- `NONCE_KEY`
- `AUTH_SALT`
- `SECURE_AUTH_SALT`
- `LOGGED_IN_SALT`
- `NONCE_SALT`

### Multi-prepend

Allows you to use multiple `auto_prepend_file` files. It reads a comma-separated list of file paths from the `MULTI_PREPEND` environment variable and includes them.

## Usage

You can enable the helpers by setting the `auto_prepend_file` directive in your `php.ini` or webserver configuration.

### Single Helper

Set the following `php.ini` variable to enable the SSL helper:

`auto_prepend_file=SSLHelper_prepend.php`

Or for Apache configuration:

```apache
<Directory /srv/www/>
    AllowOverride All
    Require all granted
   <If "-T env('BEHIND_PROXY')">
      php_value auto_prepend_file "ProxyHelper_prepend.php"
   </If>
</Directory>
```

### Multiple Helpers

To use multiple helpers, use `Multi_prepend.php` and set the `MULTI_PREPEND` environment variable with a comma-separated list of the prepend files you want to include.

**Example:**

Set `auto_prepend_file` to `Multi_prepend.php`:

`auto_prepend_file=Multi_prepend.php`

Set the `MULTI_PREPEND` environment variable:

`MULTI_PREPEND=ProxyHelper_prepend.php,WordPressHelper_prepend.php`

This will load both `ProxyHelper` and `WordPressHelper`.

## Docker installation

To install in a Docker image:

### Debian

```Dockerfile
# Install PHP Extras
RUN set -x \
  && PHPEXTRAS_VERSION=0.2.0 \
  && PHPEXTRAS_SHA256=1dc751f28ceb799d82c069807df681a9debba2e66e7f37c95ed5e80776f341d1 \
  && if ! command -v wget > /dev/null; then \
      fetchDeps="${fetchDeps} wget"; \
     fi \
  && apt-get update \
  && apt-get install -y --no-install-recommends ${fetchDeps} \
  && cd /tmp \
  && wget -nv https://github.com/panubo/php-extras/releases/download/v${PHPEXTRAS_VERSION}/php-extras.tar.gz \
  && echo "${PHPEXTRAS_SHA256}  php-extras.tar.gz" > /tmp/SHA256SUM \
  && ( cd /tmp; sha256sum -c SHA256SUM || ( echo "Expected $(sha256sum php-extras.tar.gz)"; exit 1; )) \
  && mkdir -p /usr/share/php/ \
  && tar --no-same-owner -C /usr/share/php/ -zxf php-extras.tar.gz \
  && rm -rf /tmp/* \
  && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false ${fetchDeps} \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* \
  ;
```
