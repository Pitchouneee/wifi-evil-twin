FROM php:8.4-apache

ARG CONTEXT_PATH=

# Extension required for PDO MySQL/MariaDB
RUN docker-php-ext-install pdo_mysql && \
    a2enmod rewrite

COPY . /var/www/html/${CONTEXT_PATH}/

# Redirect "/" to the context path, only when one is set
RUN if [ -n "$CONTEXT_PATH" ]; then \
      printf 'RedirectMatch ^/$ /%s/\n' "$CONTEXT_PATH" \
        > /etc/apache2/conf-enabled/context-redirect.conf; \
    fi

EXPOSE 80
