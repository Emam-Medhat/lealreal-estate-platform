FROM php:8.2-fpm
RUN apt-get update && apt-get install -y libzip-dev zip git unzip \
    && docker-php-ext-install pdo_mysql opcache
RUN printf "opcache.enable=1\\nopcache.memory_consumption=256\\nopcache.interned_strings_buffer=16\\nopcache.max_accelerated_files=20000\\nopcache.validate_timestamps=1\\nopcache.revalidate_freq=0\\nopcache.jit_buffer_size=100M\\nopcache.jit=tracing\\n" > /usr/local/etc/php/conf.d/opcache.ini
WORKDIR /var/www
CMD ["php-fpm"]
