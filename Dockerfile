# Use an official PHP runtime as a parent image
FROM webdevops/php-apache-dev

# Set the working directory
WORKDIR /app

# Copy the application code
COPY ./app /WEBFINALS
