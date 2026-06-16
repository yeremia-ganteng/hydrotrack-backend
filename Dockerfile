FROM richarvey/nginx-php-fpm:3.1.6

COPY . .

# Image config
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Izinkan composer berjalan sebagai root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Install dependencies Laravel saat perakitan server
RUN composer install --no-dev --optimize-autoloader

CMD ["/start.sh"]