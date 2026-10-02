FROM php:8.4-apache

# System libraries for intl and gd (JPEG, PNG, WebP), exif (to keep photos upright
# when their metadata is stripped), plus unzip/git for Composer
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libicu-dev libjpeg62-turbo-dev libpng-dev libwebp-dev libfreetype6-dev unzip git \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install intl mysqli gd exif \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Serve only public/ and enable rewrites for CodeIgniter routing
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite headers \
    && echo "ServerName localhost" > /etc/apache2/conf-enabled/servername.conf

# PHP settings: timezone and upload limits for complaint photos
RUN { \
      echo 'date.timezone = Asia/Jakarta'; \
      echo 'upload_max_filesize = 10M'; \
      echo 'post_max_size = 12M'; \
      echo 'memory_limit = 256M'; \
      echo 'expose_php = Off'; \
    } > /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html

COPY docker/entrypoint.sh /usr/local/bin/app-entrypoint
RUN chmod +x /usr/local/bin/app-entrypoint

EXPOSE 80
ENTRYPOINT ["app-entrypoint"]
CMD ["apache2-foreground"]
