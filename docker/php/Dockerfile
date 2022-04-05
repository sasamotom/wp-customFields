FROM php:7.4-apache
LABEL maintainer yt

# mailhog
RUN curl --location --output /usr/local/bin/mhsendmail https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64 && \
    chmod +x /usr/local/bin/mhsendmail
RUN echo 'sendmail_path="/usr/local/bin/mhsendmail --smtp-addr=mailhog:1025 --from=no-reply@docker.dev"' > /usr/local/etc/php/conf.d/mailhog.ini

# ADD
COPY ./php.ini /usr/local/etc/php
COPY ./apache2.conf /etc/apache2/conf-enabled

# xdebug install
RUN pecl install xdebug && docker-php-ext-enable xdebug
# xdebug install (php5.6)
# RUN pecl install xdebug-2.5.5 && docker-php-ext-enable xdebug

RUN apt-get update \
  && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev libmcrypt-dev vim \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install mysqli pdo_mysql gd

RUN a2enmod rewrite
