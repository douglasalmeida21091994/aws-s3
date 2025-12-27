FROM ubuntu:18.04
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update -y && apt-get upgrade -y

# Instalando PHP, extensões e o XDEBUG
RUN apt-get install -y \
    apache2 \
    php \
    php-xdebug \
    php-mysql \
    php-curl \
    php-mbstring \
    libapache2-mod-php \
    curl \
    unzip

# Configuração do Xdebug 2 (específico para PHP 7.2 no Ubuntu 18.04)
RUN echo "xdebug.remote_enable=1" >> /etc/php/7.2/mods-available/xdebug.ini && \
    echo "xdebug.remote_autostart=1" >> /etc/php/7.2/mods-available/xdebug.ini && \
    echo "xdebug.remote_port=9000" >> /etc/php/7.2/mods-available/xdebug.ini && \
    echo "xdebug.remote_connect_back=0" >> /etc/php/7.2/mods-available/xdebug.ini

WORKDIR /var/www/html
COPY src/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apachectl", "-D", "FOREGROUND"]