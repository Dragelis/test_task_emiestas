FROM php:7.3-fpm
ARG TIMEZONE

RUN apt-get update && apt-get install -y \
    openssl \
    git \
    unzip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
&& composer --version

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone \
&& printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini \
&& "date"

RUN pecl install apcu \
&& echo 'extension=apcu.so' > /usr/local/etc/php/conf.d/apcu.ini

# Type docker-php-ext-install to see available extensions
RUN docker-php-ext-install opcache pdo pdo_mysql

RUN echo 'expose_php=Off' >> /usr/local/etc/php/conf.d/additional.ini \
&& echo 'upload_max_filesize=10M' >> /usr/local/etc/php/conf.d/additional.ini \
&& echo 'post_max_size=15M' >> /usr/local/etc/php/conf.d/additional.ini

RUN echo 'alias sf4="php bin/console"' >> ~/.bashrc

WORKDIR /var/www/symfony
