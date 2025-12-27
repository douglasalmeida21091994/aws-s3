FROM ubuntu:18.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update -y && apt-get upgrade -y

# Adicionado php-mysql e php-curl
RUN apt-get install -y \
    apache2 \
    php \
    php-mysql \
    php-curl \
    php-mbstring \
    libapache2-mod-php \
    curl \
    unzip

WORKDIR /var/www/html
COPY src/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apachectl", "-D", "FOREGROUND"]