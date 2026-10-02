# Legacy CodeIgniter 3 app — kept running side by side during the CI4 migration
FROM php:8.1-apache

# Enable Apache mod_rewrite for CodeIgniter routing
RUN a2enmod rewrite

# Expose CI_ENV to PHP's $_SERVER (read by index.php to set ENVIRONMENT)
RUN echo 'PassEnv CI_ENV' > /etc/apache2/conf-enabled/ci-env.conf

# Install mysqli and pdo_mysql extensions
RUN docker-php-ext-install mysqli pdo_mysql

# PHP settings: timezone, sessions and upload limits for complaint photos
RUN { \
      echo 'date.timezone = Asia/Jakarta'; \
      echo 'session.save_path = /tmp'; \
      echo 'upload_max_filesize = 10M'; \
      echo 'post_max_size = 12M'; \
    } > /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html

EXPOSE 80
