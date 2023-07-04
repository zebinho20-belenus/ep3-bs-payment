FROM php:7.2-apache 

ENV COMPOSER_ALLOW_SUPERUSER=1

ENV ICU_RELEASE=65.1
RUN apt-get update \
    && apt-get install --no-install-recommends -y libzip-dev libsodium-dev \
    && apt-get install -y zlib1g-dev libxml2-dev \
    && apt-get install -y git build-essential autoconf file pkg-config re2c python \
    && cd /tmp && curl -Ls https://github.com/unicode-org/icu/releases/download/release-$(echo $ICU_RELEASE | tr '.' '-')/icu4c-$(echo $ICU_RELEASE | tr '.' '_')-src.tgz > icu4c-src.tgz \
    && cd /tmp && tar xzf icu4c-src.tgz && cd /tmp/icu/source && ./runConfigureICU Linux && make && make install && rm -rf /tmp/icu /tmp/icu4c-src.tgz \    
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install zip
RUN docker-php-ext-configure intl && docker-php-ext-install intl 
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN docker-php-ext-configure sodium && docker-php-ext-install sodium
RUN docker-php-ext-install soap
RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY app /var/www/html
RUN cd /var/www/html/ && composer install

RUN chown -R www-data:www-data /var/www/html/*

RUN cd /var/www/html \
    && chmod -R u+w data/cache/ \
    && chmod -R u+w data/log/ \
    && chmod -R u+w data/session/ \
    && chmod -R u+w public/docs-client/upload/ \
    && chmod -R u+w public/imgs-client/upload/ 

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf


