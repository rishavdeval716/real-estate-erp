# Production Dockerfile for CodeIgniter 4 Real Estate ERP on Render
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies + lightweight MariaDB for zero-config free deployment
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    ca-certificates \
    mariadb-server \
    mariadb-client \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Low-memory MariaDB config for Render Free Tier (512MB RAM total)
RUN { \
        echo '[mysqld]'; \
        echo 'bind-address=0.0.0.0'; \
        echo 'innodb_buffer_pool_size=32M'; \
        echo 'innodb_log_buffer_size=4M'; \
        echo 'key_buffer_size=8M'; \
        echo 'max_connections=25'; \
    } > /etc/mysql/conf.d/render-low-mem.cnf

# Configure & install PHP extensions required by CodeIgniter 4
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        intl \
        mbstring \
        mysqli \
        pdo_mysql \
        gd \
        zip \
        opcache

# Configure OPcache for low-memory, fast execution on Render Free Tier
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=64'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.revalidate_freq=0'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.save_comments=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Enable Apache modules: rewrite, headers, env
RUN a2enmod rewrite headers env

# Configure Apache virtual host with exact DocumentRoot /var/www/html/public
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf

# Ensure Apache global configuration allows override for /var/www/
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Copy Composer binary from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application source code
COPY . /var/www/html

# Install Composer dependencies (production mode)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Create and set permissions for CodeIgniter writable directories
RUN mkdir -p /var/www/html/writable/cache \
             /var/www/html/writable/logs \
             /var/www/html/writable/session \
             /var/www/html/writable/uploads \
             /var/www/html/writable/debugbar \
    && chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Render sets PORT env variable (e.g. 10000) - default to 80
ENV PORT=80
EXPOSE ${PORT}

# Set entrypoint and default command
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
