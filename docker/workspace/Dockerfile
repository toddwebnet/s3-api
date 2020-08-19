ARG PHP_DOCKERFILE_SOURCE=I_WILL_FAIL
FROM ${PHP_DOCKERFILE_SOURCE}

ENV DEBIAN_FRONTEND noninteractive


# Install "PHP Extentions", "libraries", "Software's"
RUN apt-get update && \
    apt-get install -y \
        git \
        curl \
        nano \
        zip unzip  \
        libcurl3-openssl-dev \
        libonig-dev \
        libzip-dev \
        libpng-dev \
        libicu-dev \
        libxml2-dev \
        libpq-dev \
        libz-dev libmemcached-dev \
        libjpeg-dev zlib1g-dev libpng-dev \
        libgif-dev libgd3 libgd-dev \
        gcc make autoconf libc-dev pkg-config libmcrypt-dev

#RUN docker-php-ext-install memcached
RUN pecl install memcached


RUN docker-php-ext-install curl
RUN docker-php-ext-install intl
RUN docker-php-ext-install json
RUN docker-php-ext-install xml
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pgsql
# RUN docker-php-ext-install sqlite
# RUN docker-php-ext-install sqlite3
RUN docker-php-ext-install zip
#RUN docker-php-ext-configure gd --with-jpeg
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install bcmath

RUN docker-php-ext-install pdo_mysql


RUN apt-get install default-mysql-client -y
RUN apt-get install default-libmysqlclient-dev -y


RUN apt-get install default-mysql-client -y
RUN apt-get install default-libmysqlclient-dev -y

RUN  apt-get clean



RUN curl -s http://getcomposer.org/installer | php && \
    echo "export PATH=${PATH}:/var/www/vendor/bin" >> ~/.bashrc && \
    mv composer.phar /usr/local/bin/composer

# Source the bash
RUN . ~/.bashrc


WORKDIR /var/www
