# Use an official PHP runtime as a parent image
FROM php:7.4-apache

# Set the working directory
WORKDIR /var/www/html

# Copy the application code
COPY . .

# Install dependencies if needed
# RUN docker-php-ext-install mysqli pdo pdo_mysql

# Expose the port the app runs on
EXPOSE 80

# Define the command to run the application
CMD ["apache2-foreground"]
