FROM php:8.1-fpm

# Install system dependencies
RUN apt update && apt install --yes --allow-downgrades --allow-remove-essential --allow-change-held-packages \
    make \
    curl \
    bash \
    nano \
    unzip \
    libicu-dev \
    git
    
# Install PHP extensions script
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install PHP extensions
RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && install-php-extensions intl zip xsl pdo pdo_mysql @composer


# Install Composer
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer


