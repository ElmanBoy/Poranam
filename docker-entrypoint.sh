#!/bin/bash
# Отключаем HTTPS-редирект в .htaccess для локальной разработки
HTACCESS=/var/www/html/.htaccess
if [ -f "$HTACCESS" ]; then
    sed -i '/^RewriteCond %{SERVER_PORT} 80/s/^/#/' "$HTACCESS"
    sed -i '/^RewriteRule .\*\$ https:\/\/%{SERVER_NAME}\//s/^/#/' "$HTACCESS"
fi

# Запускаем Apache
exec apache2-foreground
