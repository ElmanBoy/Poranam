FROM php:7.4-apache

# Включаем mod_rewrite
RUN a2enmod rewrite

# Устанавливаем расширения PHP
RUN docker-php-ext-install mysqli

# Разрешаем .htaccess переопределять настройки
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Конфигурация виртуального хоста
RUN echo '<VirtualHost *:80>\n\
    ServerName iskramet.com\n\
    ServerAlias *\n\
    DocumentRoot /var/www/html\n\
    <Directory /var/www/html>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Подменяем SERVER_NAME для локальной разработки
RUN echo 'auto_prepend_file=/var/www/html/docker_local.php' > /usr/local/etc/php/conf.d/docker-local.ini

# Entrypoint патчит .htaccess и запускает Apache
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
CMD ["/usr/local/bin/docker-entrypoint.sh"]
