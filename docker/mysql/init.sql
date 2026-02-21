CREATE DATABASE IF NOT EXISTS military_db;
CREATE DATABASE IF NOT EXISTS military_test_db;

CREATE DATABASE IF NOT EXISTS military_logger_db;
CREATE DATABASE IF NOT EXISTS military_symfony_db;

GRANT ALL PRIVILEGES ON military_db.* TO 'laravel'@'%';
GRANT ALL PRIVILEGES ON military_test_db.* TO 'laravel'@'%';

GRANT ALL PRIVILEGES ON military_logger_db.* TO 'laravel'@'%';
GRANT ALL PRIVILEGES ON military_symfony_db.* TO 'laravel'@'%';

FLUSH PRIVILEGES;