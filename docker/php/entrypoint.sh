#!/bin/bash

cd /var/www

echo "Waiting for MySQL to be ready"
while ! nc -z military_db 3306; do
  sleep 1
done
echo "MySQL is ready!"

echo "Waiting for RabbitMQ to be ready"
while ! nc -z military_rabbitmq 5672; do
  sleep 1
done
echo "RabbitMQ is ready!"

if [ ! -d "vendor" ]; then
  echo "Installing Composer dependencies"
    composer install
fi

sudo chown -R laravel:laravel /var/www/storage /var/www/bootstrap/cache
sudo chmod -R 775 /var/www/storage /var/www/bootstrap/cache

if [ "$SERVICE_ROLE" = "core" ]; then
    echo "Running Migrations & Seeds"
    php artisan migrate:fresh --seed --force

    echo "Importing to Elasticsearch"
    php artisan scout:import "App\Models\Soldier"
    php artisan scout:import "App\Models\Warehouse"

elif [ "$SERVICE_ROLE" = "logger" ]; then
    echo "Running Migrations for Logger"
    php artisan migrate --force

else
    echo "Unknown role or no role set. Skipping specific tasks."
fi

echo "Starting Supervisor"
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf