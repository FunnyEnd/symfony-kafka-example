FROM php:8.1-fpm

ENV TZ=Europe/Kiev
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

WORKDIR /var/www
ADD . /var/www

RUN chmod +x docker/init_app.sh
RUN apt update
RUN apt-get install -y gnupg
RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 3B4FE6ACC0B21F32
RUN echo "deb http://security.ubuntu.com/ubuntu bionic-security main" >> /etc/apt/sources.list
RUN apt-cache policy libssl1.0-dev
RUN apt-get update && apt-get install -y libpq-dev git libzip-dev zip unzip curl vim htop openssl libssl1.0-dev
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql
RUN pecl install docker-php-ext-install zip
RUN pecl docker-php-ext-install php-mbstring

RUN apt install -y librdkafka-dev
RUN pecl install rdkafka
ADD docker/config/rdkafka.ini /usr/local/etc/php/conf.d/rdkafka.ini

RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys ABF5BD827BD9BF62
RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C
RUN echo "deb http://nginx.org/packages/ubuntu/ trusty nginx" >> /etc/apt/sources.list
RUN echo "deb-src http://nginx.org/packages/ubuntu/ trusty nginx" >> /etc/apt/sources.list
RUN apt update
RUN apt-get install -y nginx

ADD docker/config/default /etc/nginx/sites-enabled/
ADD docker/config/nginx-main.conf /etc/nginx/nginx.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get install -y supervisor
RUN mkdir -p /var/log/supervisor
ADD docker/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

#RUN composer i --no-dev

EXPOSE 80

CMD ["docker/init_app.sh"]
