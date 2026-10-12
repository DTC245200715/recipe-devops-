FROM php:8.2-fpm-alpine

# Install runtime dependencies for health checks and PDO MySQL
RUN apk add --no-cache curl && \
    docker-php-ext-install pdo pdo_mysql

# Create a dedicated non-root user with a restricted home directory
RUN addgroup -g 1000 appuser && \
    adduser -u 1000 -G appuser -s /bin/sh -D appuser && \
    mkdir -p /var/www/html/public/uploads && \
    chown -R appuser:appuser /var/www/html

WORKDIR /var/www/html

USER appuser

