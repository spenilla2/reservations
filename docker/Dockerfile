# Imagen de la app para desarrollo local: PHP 7.4 + Apache + mysqli, parecido al hosting.
FROM docker.io/library/php:7.4-apache
RUN docker-php-ext-install mysqli calendar
COPY php.ini /usr/local/etc/php/conf.d/sirr.ini
