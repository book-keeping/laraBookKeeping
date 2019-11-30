FROM php:7.3-apache
LABEL  maintainer "haessal <haessal@mizutamauki.net>"

# Enabla apache module 'rewrite'
RUN a2enmod rewrite

# Install 'unzip' command. (It is used by Composer)
ENV DEBCONF_NOWARNINGS yes
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        unzip

# Install the PHP pdo_mysql extention
RUN docker-php-ext-install pdo_mysql

# Install Composer
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Copy the code of BookKeeping
COPY ./book-keeping /var/book-keeping

# Install the packages and modify files related to apache as follows
#  - Change owner of files that are loaded to apache
#  - Link from apache Document Root
#  - Remove a file for IIS (not for apache)
RUN cd /var/book-keeping \
    && composer install --optimize-autoloader --no-dev \
    && chown -R www-data:www-data /var/book-keeping \
    && rmdir ../www/html && ln -s /var/book-keeping/public ../www/html \
    && rm /var/book-keeping/public/web.config

# Set up directory for logging
RUN rm -R /var/log/apache2 \
    && mkdir -p /var/log/bookkeeping/laravel \
    && cd /var/book-keeping \
    && rm storage/logs/.gitignore && rmdir storage/logs && ln -s /var/log/bookkeeping/laravel storage/logs
ENV APACHE_LOG_DIR /var/log/bookkeeping/apache2
