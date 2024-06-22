## NOTES:

### Composer

``` bash
podman run --rm --interactive --tty \
  --volume .:/app \
  composer dump-autoload
```

``` bash
podman run --rm --interactive --tty \
  --volume .:/app \
  composer install
```

### Formatting

``` bash
podman run --rm -v .:/app php:8.2-cli-alpine /app/vendor/bin/pretty-php /app/src
```
