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

if [ ! -d "vendor" ] && [ -f "composer.json" ]; then
  echo "Installing Composer dependencies"
  composer install
fi

if [ "$SERVICE_ROLE" = "core" ]; then
  sudo chown -R laravel:laravel /var/www/storage /var/www/bootstrap/cache
  sudo chmod -R 775 /var/www/storage /var/www/bootstrap/cache
  echo "Running Migrations & Seeds"
  php artisan migrate:fresh --seed --force

  echo "Importing to Elasticsearch"
  php artisan scout:import "App\Models\Soldier"
  php artisan scout:import "App\Models\Warehouse"

  echo "Starting Supervisor"
  exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf

elif [ "$SERVICE_ROLE" = "logger" ]; then
  sudo chown -R laravel:laravel /var/www/storage /var/www/bootstrap/cache
  sudo chmod -R 775 /var/www/storage /var/www/bootstrap/cache
  echo "Running Migrations for Logger"
  php artisan migrate --force

  echo "Importing Logs to Elasticsearch"
  php artisan scout:import "App\Models\Log"

  echo "Starting Supervisor"
  exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf

elif [ "$SERVICE_ROLE" = "symfony" ]; then
  sudo chown -R laravel:laravel /var/www/var
  sudo chmod -R 775 /var/www/var

  echo "Running Migrations for Symfony"
  php bin/console doctrine:migrations:migrate --no-interaction

  echo "Starting Supervisor for Symfony"
  exec /usr/bin/supervisord -c /etc/supervisor/supervisord-symfony.conf
  echo "Starting PHP-FPM for Symfony"
  exec php-fpm

else
    echo "Unknown role or no role set. Skipping specific tasks."
fi

echo "Starting Supervisor"