# PHP Extras

Collection of helper PHP classes. These are used by [docker-php-apache](https://github.com/panubo/docker-php-apache) and [docker-apache-mvh](https://github.com/panubo/docker-apache-mvh) containers. These are fairly specific at the moment but we hope to make then more generic in the future. We welcome contributions.

## Usage Example

Set the following `php.ini` variable to enable the SSL helper:

`auto_prepend_file=SSLHelper_prepend.php`

or for Apache conf:

```
<Directory /srv/www/>
    AllowOverride All
    Require all granted
   <If "-T env('BEHIND_PROXY')">
      php_value auto_prepend_file "ProxyHelper_prepend.php"
   </If>
</Directory>
```

## Docker installation

To install in a Docker image:

```Dockerfile
# Install PHP Extras
RUN cd /tmp \
  && curl -s https://github.com/panubo/php-extras/archive/master.tar.gz -o /tmp/master.tar.gz \
  && tar --wildcards -C /usr/share/php/ -xvf master.tar.gz --strip 1 '*.php'  \
  && rm -f /tmp/master.tar.gz
```
