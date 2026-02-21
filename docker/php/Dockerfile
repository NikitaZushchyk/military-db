FROM php:8.2-fpm

ARG user
ARG uid

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev \
    zip unzip libzip-dev supervisor default-mysql-client \
    netcat-openbsd procps sudo librabbitmq-dev\
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip sockets
RUN pecl install redis amqp && docker-php-ext-enable redis amqp

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

RUN echo "$user ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers.d/$user

RUN mkdir -p /var/log/supervisor && chown -R $user:$user /var/log/supervisor
RUN chown -R $user:$user /var/log

RUN sed -i "s/user = www-data/user = $user/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = $user/g" /usr/local/etc/php-fpm.d/www.conf

COPY docker/php/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www

USER $user

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]