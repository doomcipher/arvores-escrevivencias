FROM php:8.2-apache

# Apache: habilita rewrite (essencial pro MVC com .htaccess)
RUN a2enmod rewrite

# Dependências para extensões comuns
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libzip-dev libonig-dev \
 && rm -rf /var/lib/apt/lists/*

# Extensões PHP (DB e utilidades)
RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Opcional: ajustar DocumentRoot (se quiser servir /public direto)
# ENV APACHE_DOCUMENT_ROOT=/var/www/html
# RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
#  && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/project
