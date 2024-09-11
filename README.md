## DOCS:

### Environment Variables
``` bash
ENVIRONMENT="production"
AUTH_URL="http://auth:9000/auth"
DATABASE_URL="http://surrealdb:8000/sql"
```

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

### Testing
``` bash
podman run --rm -v .:/app php:8.2-cli-alpine /app/vendor/bin/phpunit /app/tests
```

### Formatting
``` bash
podman run --rm -v .:/app php:8.2-cli-alpine /app/vendor/bin/pretty-php /app/src
```

### Building

#### images

**create builder**
``` bash
docker buildx create --name container-builder --driver docker-container --use --bootstrap
```

**create image**
``` bash
docker buildx build --tag kennycallado/q-app-admin:v<version> --platform linux/arm64,linux/amd64 --builder container-builder --push .
```
