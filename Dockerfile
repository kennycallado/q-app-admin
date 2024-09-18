##
FROM composer:lts AS composer

COPY ./composer.json /app/composer.json
COPY ./composer.lock /app/composer.lock

WORKDIR /app
RUN composer install --no-dev --no-scripts
RUN composer dump-autoload

##
FROM node:lts-alpine AS node

COPY ./package.json /app/package.json

WORKDIR /app
RUN npm install
RUN npm run build

RUN rm -rf node_modules
RUN npm install --omit=dev

##
FROM php:fpm-alpine

RUN apk add --no-cache nginx

COPY ./nginx.conf /etc/nginx/http.d/default.conf

COPY --from=node /app/node_modules /var/www/html/node_modules
COPY --from=composer /app/vendor /var/www/html/vendor

COPY ./public /var/www/html/public
COPY ./src /var/www/html/src

# change to production in production
ENV ENVIRONMENT=development
EXPOSE 80

CMD php-fpm -D && nginx -g 'daemon off;'
