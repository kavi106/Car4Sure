FROM node:alpine as builder

WORKDIR /app

COPY package.json .

RUN npm install

COPY . .

RUN npm run build

FROM php:8.2.7-alpine

WORKDIR /var/www/api

RUN apk add bash

COPY . .

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');"

ENV COMPOSER_ALLOW_SUPERUSER 1
# ENV APP_ENV prod
# ENV DATABASE_URL "sqlite:///./var/data.db"

RUN php composer.phar install
RUN php bin/console doctrine:database:create
RUN rm -r ./migrations/*
RUN php bin/console make:migration
RUN php bin/console doctrine:migrations:migrate

COPY --from=builder /app/public/build /var/www/api/public/build

CMD [ "php", "-S", "0.0.0.0:8000", "-t", "./public" ]