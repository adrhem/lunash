FROM php:8.4-fpm-alpine

RUN apk add --no-cache ${PHPIZE_DEPS} \
    zip \
    libzip-dev \
    icu-dev \
    nginx \
    curl-dev \
    openssl-dev \
    bash \
    docker \ 
    docker-cli-compose

RUN docker-php-ext-install zip intl \
    && docker-php-ext-enable zip intl

COPY --from=composer:2.8.11 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN pecl install mongodb

RUN docker-php-ext-enable mongodb

VOLUME ["/var/www/html/"]

# Specific to Alpine Linux
COPY ./nginx.conf /etc/nginx/http.d/default.conf

EXPOSE 80

CMD ["sh", "-c", "nginx && php-fpm"]