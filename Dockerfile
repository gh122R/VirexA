FROM php:8.2-fpm

# Установим nginx и supervisor
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
 && apt-get clean

# Копируем nginx конфиг
COPY ./docker/nginx/conf.d/ /etc/nginx/conf.d/

# Копируем файл supervisor конфигурации
COPY ./supervisord.conf /etc/supervisord.conf

# Устанавливаем рабочую директорию
WORKDIR /var/www

# Копируем код проекта внутрь контейнера
COPY . .

# Убедимся, что php-fpm.sock директория существует
RUN mkdir -p /run/php

# Порт, который будет слушать nginx
EXPOSE 80

# Запуск supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
