FROM php:8.3-fpm-alpine

ENV UID=1000
ENV GID=1000

RUN mkdir -p /var/www/html

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN delgroup dialout \
    && addgroup -g ${GID} --system laravel \
    && adduser -G laravel --system -D -s /bin/sh -u ${UID} laravel

RUN sed -i "s/user = www-data/user = laravel/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/group = www-data/group = laravel/g" /usr/local/etc/php-fpm.d/www.conf \
    && echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf

RUN echo "http://dl-cdn.alpinelinux.org/alpine/v3.15/main" >> /etc/apk/repositories \
    && echo "http://dl-cdn.alpinelinux.org/alpine/v3.15/community" >> /etc/apk/repositories \
    && apk update

RUN apk add --no-cache \
    nginx \
    tzdata \
    autoconf \
    g++ \
    make \
    zip \
    libzip-dev \
    gettext \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo pdo_mysql \
    && apk upgrade --no-cache

# Timezone para America/Maceio
RUN cp /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime \
&& echo "America/Maceio" > /etc/timezone \
&& echo "date.timezone = America/Maceio" >> /usr/local/etc/php/conf.d/timezone.ini

# Redis
RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/5.3.4.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/local/etc/php-available-exts \
    && docker-php-ext-install redis

EXPOSE 9000

CMD ["php-fpm", "-F"]