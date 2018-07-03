FROM php:7.2-cli-alpine3.7
MAINTAINER tienvx <tien.xuan.vo@gmail.com>

RUN apk add --no-cache bash $PHPIZE_DEPS && \
    pecl install xdebug && \
    docker-php-ext-install pdo pdo_mysql && \
    docker-php-ext-enable xdebug && \
    apk del $PHPIZE_DEPS

WORKDIR /app

EXPOSE 80

CMD ["/app/resources/entrypoint.sh"]
