#
# See https://hub.docker.com/_/php/
# See https://github.com/docker-library/php/blob/6677546d599d3980781b520f84d03ecaad291dd1/7.2/stretch/fpm/Dockerfile
#

FROM php:7.4-fpm
MAINTAINER BSW

#
# Copy source package
#
COPY src src-package

#
# For writable
#
RUN usermod -u 1000 www-data

# MAC OSX
# RUN usermod -u 1000 www-data && usermod -G staff www-data

#
# Install software
#
RUN apt-get update && apt-get install -y \
    git \
    python3 \
    python3-distutils \
    libfreetype6 \
    libfreetype6-dev \
    libjpeg62-turbo \
    libjpeg62-turbo-dev \
    mcrypt \
    libmcrypt4 \
    libmcrypt-dev \
    libmhash2 \
    libpng-tools \
    libpng-dev \
    libpng16-16 \
    zlib1g-dev \
    openssl \
    libssh-dev \
    libnghttp2-dev \
    libhiredis-dev \
    libicu-dev \
    libzip-dev \
    wget \
    unzip \
    zip \
    sshpass \
    net-tools \
    telnet \
    procps

#
# Install sphinx-document
#
RUN wget --no-check-certificate https://pypi.python.org/packages/72/c2/c09362ab29338413ab687b47dab03bab4a792e2bbb727a1eb5e0a88e3b86/setuptools-39.0.1.zip \
    && unzip setuptools-39.0.1.zip \
    && ( \
        cd setuptools-39.0.1 \
        && python3 setup.py build \
        && python3 setup.py install \
    ) \
    && wget --no-check-certificate  https://pypi.python.org/packages/source/p/pip/pip-8.0.2.tar.gz \
    && tar -zxvf pip-8.0.2.tar.gz \
    && ( \
        cd pip-8.0.2 \
        && python3 setup.py build \
        && python3 setup.py install \
    ) \
    && pip3 install --upgrade pip \
    && pip3 install sphinx sphinx_rtd_theme

#
# Install software ZLIB
#
RUN curl -fsSL 'http://www.zlib.net/zlib-1.2.11.tar.gz' -o zlib.tar.gz \
    && mkdir -p /tmp/zlib \
    && tar -xf zlib.tar.gz -C /tmp/zlib --strip-components=1 \
    && rm -f zlib.tar.gz \
    && ( \
        cd /tmp/zlib \
        && ./configure \
        && make -j$(nproc) \
        && make install \
    ) \
    && rm -r /tmp/zlib

#
# EXT intl with dependent ICU
#
RUN if [ -f "src-package/icu.tgz" ]; then cp src-package/icu.tgz .; else curl -fsSL 'http://download.icu-project.org/files/icu4c/60.2/icu4c-60_2-src.tgz' -o icu.tgz; fi \
    && mkdir -p /tmp/icu \
    && tar -xf icu.tgz -C /tmp/icu --strip-components=1 \
    && rm -f icu.tgz \
    && ( \
        cd /tmp/icu/source \
        && ./configure --prefix=/usr/local/icu \
        && make -j$(nproc) \
        && make install \
    ) \
    # php7.2
    # && docker-php-ext-configure intl --enable-intl --with-icu-dir=/usr/local/icu \
    && docker-php-ext-configure intl --enable-intl \
    && docker-php-ext-install -j$(nproc) intl

#
# EXT imagick with dependent ImageMagick
#
RUN if [ -f "src-package/imagemagick.tar.gz" ]; then cp src-package/imagemagick.tar.gz .; else curl -fsSL 'https://www.imagemagick.org/download/ImageMagick.tar.gz' -o imagemagick.tar.gz; fi \
    && mkdir -p /tmp/imagemagick \
    && tar -xf imagemagick.tar.gz -C /tmp/imagemagick --strip-components=1 \
    && rm -f imagemagick.tar.gz \
    && ( \
        cd /tmp/imagemagick \
        && ./configure --prefix=/usr/local/imagemagick --with-jpeg=yes --with-png=yes \
        && make -j$(nproc) \
        && make install \
    ) \
    && rm -r /tmp/imagemagick \
    && curl -fsSL 'http://pecl.php.net/get/imagick-3.4.3.tgz' -o imagick.tgz \
    && mkdir -p /tmp/imagick \
    && tar -xf imagick.tgz -C /tmp/imagick --strip-components=1 \
    && rm -f imagick.tgz \
    && docker-php-ext-configure /tmp/imagick --with-imagick=/usr/local/imagemagick \
    && docker-php-ext-install /tmp/imagick \
    && rm -r /tmp/imagick

#
# EXT gd、zip
#
RUN docker-php-ext-configure \
    gd \
        # php7.2
        # --with-freetype-dir=/usr/include --with-jpeg-dir=/usr/include \
        --with-freetype=/usr/include --with-jpeg=/usr/include \
    && docker-php-ext-install -j$(nproc) gd zip

#
# Ext install by PECL
#
RUN pecl install redis-5.2.0 \
    &&  pecl install xdebug-2.9.2 \
    &&  pecl install mcrypt-1.0.3 \
    && ( \
        cd /root \
        && pecl download swoole-4.4.16 \
        && tar -zxvf swoole-* \
        && cd swoole-* \
        && phpize \
        && ./configure --enable-openssl  --enable-http2  --enable-async-redis \
        && make -j$(nproc) \
        && make install \
    ) \
    &&  docker-php-ext-enable redis xdebug mcrypt swoole

#
# Ext postgresql for swoole by bin
#
RUN apt-get install -y libpq-dev \
    && git clone -b v4.4.16 https://github.com/swoole/ext-postgresql.git /source-code/swoole-pg \
    && ( \
        cd /source-code/swoole-pg \
        && phpize \
        && ./configure \
        && make -j$(nproc) \
        && make install \
    ) \
    &&  docker-php-ext-enable swoole_postgresql

#
# Ext core extensions
#
# Possible values for ext-name:
#
# bcmath bz2 calendar ctype curl dba dom enchant exif fileinfo
# filter ftp gd gettext gmp hash iconv imap interbase intl json
# ldap mbstring mysqli oci8 odbc opcache pcntl pdo pdo_dblib
# pdo_firebird pdo_mysql pdo_oci pdo_odbc pdo_pgsql pdo_sqlite
# pgsql phar posix pspell readline recode reflection session
# shmop simplexml snmp soap sockets sodium spl standard sysvmsg
# sysvsem sysvshm tidy tokenizer wddx xml xmlreader xmlrpc
# xmlwriter xsl zend_test zip
#
RUN docker-php-ext-install -j$(nproc) \
    bcmath \
    calendar \
    dba \
    exif \
    gettext \
    mysqli \
    pcntl \
    pdo_mysql \
    shmop \
    sockets \
    sysvmsg \
    sysvsem \
    sysvshm

# -- eof --
