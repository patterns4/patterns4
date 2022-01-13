FROM php:8.0.5
RUN apt-get update -y && apt-get install -y openssl zip unzip git
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo pdo_mysql
# RUN apt-get -y install locales
# ENV LANG sv_SE.UTF-8
# ENV LC_ALL sv_SE.UTF-8

WORKDIR /app
COPY . /app
RUN composer install

CMD php artisan serve --host=0.0.0.0 --port=8000
# EXPOSE 8000
# EXPOSE 5000