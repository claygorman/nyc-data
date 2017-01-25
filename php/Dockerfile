FROM php:7.1-fpm
RUN apt-get update && apt-get install -y \
        apt-transport-https \
        git \
        cron \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libscrypt-dev \
        libpng12-dev \
        mysql-client \
        libbz2-dev \
        libssl-dev \
        libcurl4-gnutls-dev \
        libedit-dev \
        libgnutls28-dev \
        libc-client-dev libkrb5-dev \
        zlib1g-dev libicu-dev g++ unixODBC-dev libsnmp-dev libxml2-dev libxslt1-dev \
    && docker-php-ext-install -j$(nproc) iconv mcrypt zip mysqli bcmath bz2 calendar ftp intl shmop snmp soap sockets sysvmsg sysvsem sysvshm xsl mbstring dom curl json readline zip \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql pcntl \
    && rm -rf /var/lib/apt/lists/*
RUN set -x \
    && docker-php-source extract \
    && cd /usr/src/php/ext/odbc \
    && phpize \
    && sed -ri 's@^ *test +"\$PHP_.*" *= *"no" *&& *PHP_.*=yes *$@#&@g' configure \
    && ./configure --with-unixODBC=shared,/usr \
    && docker-php-ext-install odbc \
    && docker-php-source delete
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list \
    && curl -sL https://deb.nodesource.com/setup_7.x | bash - \
    && apt-get update && apt-get install -y yarn nodejs && rm -rf /var/lib/apt/lists/*
RUN curl -fsSL 'https://pecl.php.net/get/propro-2.0.1.tgz' -o propro.tar.gz \
    && mkdir -p propro \
    && tar -xf propro.tar.gz -C propro --strip-components=1 \
    && rm propro.tar.gz \
    && ( \
        cd propro \
        && phpize \
        && ./configure --with-pic --enable-shared --enable-propro --disable-static \
        && make -j$(nproc) \
        && make install \
    ) \
    && rm -r propro \
    && docker-php-ext-enable propro
RUN curl -fsSL 'https://pecl.php.net/get/raphf-2.0.0.tgz' -o raphf.tar.gz \
    && mkdir -p raphf \
    && tar -xf raphf.tar.gz -C raphf --strip-components=1 \
    && rm raphf.tar.gz \
    && ( \
        cd raphf \
        && phpize \
        && ./configure --with-pic --enable-shared --enable-raphf --disable-static \
        && make -j$(nproc) \
        && make install \
    ) \
    && rm -r raphf \
    && docker-php-ext-enable raphf
RUN curl -fsSL 'https://pecl.php.net/get/pecl_http-3.0.0.tgz' -o pecl_http.tar.gz \
    && mkdir -p pecl_http \
    && tar -xf pecl_http.tar.gz -C pecl_http --strip-components=1 \
    && rm pecl_http.tar.gz \
    && ( \
        cd pecl_http \
        && phpize \
        && ./configure --with-pic --enable-shared --with-http --disable-static \
        && make -j$(nproc) \
        && make install \
    ) \
    && rm -r pecl_http \
    && docker-php-ext-enable http
RUN curl -fsSL 'https://pecl.php.net/get/mailparse-3.0.2.tgz' -o mailparse.tar.gz \
    && mkdir -p mailparse \
    && tar -xf mailparse.tar.gz -C mailparse --strip-components=1 \
    && rm mailparse.tar.gz \
    && ( \
        cd mailparse \
        && phpize \
        && ./configure --with-pic --enable-shared --with-mailparse --disable-static \
        && make -j$(nproc) \
        && make install \
    ) \
    && rm -r mailparse \
    && docker-php-ext-enable mailparse
RUN curl -fsSL 'https://pecl.php.net/get/mongodb-1.2.2.tgz' -o mongodb.tar.gz \
    && mkdir -p mongodb \
    && tar -xf mongodb.tar.gz -C mongodb --strip-components=1 \
    && rm mongodb.tar.gz \
    && ( \
        cd mongodb \
        && phpize \
        && ./configure --with-pic --enable-shared --with-mongodb --disable-static \
        && make -j$(nproc) \
        && make install \
    ) \
    && rm -r mongodb \
    && docker-php-ext-enable mongodb
RUN curl -fsSL 'https://pecl.php.net/get/igbinary-2.0.1.tgz' -o igbinary.tar.gz \
    && mkdir -p igbinary \
    && tar -xf igbinary.tar.gz -C igbinary --strip-components=1 \
    && rm igbinary.tar.gz \
    && ( \
        cd igbinary \
        && phpize \
        && ./configure --with-pic --enable-shared --with-igbinary --disable-static \
        && make -j$(nproc) \
        && make install \
    ) \
    && rm -r igbinary \
    && docker-php-ext-enable igbinary
RUN curl -fsSL 'https://github.com/phpredis/phpredis/archive/develop.tar.gz' -o phpredis.tar.gz \
    && mkdir -p phpredis \
    && tar -xf phpredis.tar.gz -C phpredis --strip-components=1 \
    && rm phpredis.tar.gz \
    && ( \
        cd phpredis \
        && phpize \
        && ./configure --with-pic --enable-shared --enable-redis-igbinary --disable-static \
        && make -j$(nproc) \
        && make install \
    ) \
    && rm -r phpredis \
    && docker-php-ext-enable redis
COPY config/php.ini /usr/local/etc/php/
COPY config/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
EXPOSE 9000
CMD php-fpm
