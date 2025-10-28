# Base image
FROM php:8.2-fpm

# Install Nginx
RUN apt-get update && apt-get install -y nginx && rm -rf /var/lib/apt/lists/*

# Copy project files
COPY . /var/www/html

# Copy Nginx config
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Expose port 80
EXPOSE 80

# Start both PHP-FPM and Nginx
CMD service php8.2-fpm start && nginx -g "daemon off;"
