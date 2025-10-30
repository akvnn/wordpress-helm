FROM wordpress:6.8.2-php8.3-apache

# Copy custom wp-content
COPY wp-content/ /var/www/html/wp-content/

# Copy custom wp-config
COPY wp-config.php /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/wp-content
RUN chmod -R 755 /var/www/html/wp-content

# Custom PHP settings for PHP 8.3
COPY <<EOF /usr/local/etc/php/conf.d/custom.ini
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300
memory_limit = 256M
max_input_vars = 3000
EOF