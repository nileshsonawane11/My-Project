# Use official PHP image with FPM
FROM php:8.2-fpm

# Install Nginx and dependencies
RUN apt-get update && apt-get install -y nginx supervisor && rm -rf /var/lib/apt/lists/*

# Copy project files
COPY . /var/www/html

# Copy Nginx configuration
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Supervisor config to run both Nginx and PHP-FPM
RUN mkdir -p /etc/supervisor/conf.d
COPY nginx/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose web port
EXPOSE 80

# Start Supervisor (it runs both php-fpm and nginx)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
