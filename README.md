# 🛡️ Military Database Management System

A comprehensive web application for managing military personnel, equipment, assignments, and duty rosters. Built with Laravel backend and Vue.js frontend, this system provides a complete solution for military unit administration.

## 📋 Table of Contents

- [Overview](#overview)
- [Tech Stack](#tech-stack)
- [Features](#features)
- [Project Structure](#project-structure)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [API Endpoints](#api-endpoints)
- [Database Schema](#database-schema)
- [Development](#development)

## 🎯 Overview

This Military Database Management System is designed to streamline the administration of military units. It provides functionalities for:

- **Personnel Management**: Track soldiers with their ranks, units, and status
- **Equipment Management**: Manage warehouse inventory and equipment assignments
- **Duty Roster Management**: Schedule and track duty assignments
- **Assignment Tracking**: Monitor equipment issuance and returns
- **Activity Logging**: Automatic logging of all system activities
- **Dashboard Analytics**: Real-time statistics and overview

## 🛠️ Tech Stack

### Backend
- **PHP 8.2+** - Server-side language
- **Laravel 12** - PHP framework
- **Laravel Sanctum** - API authentication
- **Laravel Telescope** - Debugging and monitoring
- **MySQL 8.0** - Database
- **Redis** - Caching and sessions

### Frontend
- **Vue.js 3** - Progressive JavaScript framework
- **Vue Router 4** - Client-side routing
- **Pinia** - State management
- **Axios** - HTTP client
- **Vite** - Build tool and development server

### Infrastructure
- **Docker & Docker Compose** - Containerization
- **Nginx** - Web server
- **Composer** - PHP dependency management
- **Node.js & NPM** - JavaScript package management

## ✨ Features

### 1. Personnel Management (Soldiers)
- Create, read, update, and delete soldier records
- Filter by unit, rank, and status
- Search by name
- View soldier details including assignments and duties
- Track soldier status (active, hospital, vacation, fired)

### 2. Warehouse Management
- Manage equipment inventory
- Track equipment by serial number and type
- Monitor equipment status (in_stock, issued, broken)
- Full CRUD operations for warehouse items

### 3. Equipment Assignments
- Issue equipment to soldiers
- Return equipment and update status automatically
- View assignment history
- Track active assignments
- Automatic status updates via database triggers

### 4. Duty Roster
- Schedule duty assignments
- Assign soldiers to different duty types
- Track duty periods with start and end times
- View current duty assignments

### 5. Dashboard & Statistics
- Real-time statistics (cached for performance)
- Soldier count
- Equipment availability (free/issued)
- Active duty assignments count
- Quick overview of system status

### 6. Activity Logging
- Automatic logging of all major actions
- Track soldier creation/deletion
- Monitor equipment changes
- View comprehensive activity logs

### 7. Authentication & Security
- Laravel Sanctum token-based authentication
- Protected API routes
- Secure password hashing
- Bearer token authentication

## 📁 Project Structure

```
military-db/
├── src/                          # Laravel backend
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/     # API controllers
│   │   │   ├── Requests/        # Form request validation
│   │   │   └── Resources/       # API resources
│   │   ├── Models/              # Eloquent models
│   │   ├── Observers/           # Model observers
│   │   ├── Providers/           # Service providers
│   │   └── Services/            # Business logic services
│   ├── database/
│   │   ├── migrations/          # Database migrations
│   │   ├── seeders/             # Database seeders
│   │   └── factories/           # Model factories
│   ├── routes/
│   │   └── api.php              # API routes
│   └── config/                  # Configuration files
├── front/                        # Vue.js frontend
│   ├── src/
│   │   ├── views/               # Vue components (pages)
│   │   ├── router/              # Vue Router configuration
│   │   ├── stores/              # Pinia stores
│   │   └── App.vue              # Root component
│   └── dist/                    # Build output
├── docker/                       # Docker configuration
│   ├── php/
│   │   └── Dockerfile           # PHP-FPM container
│   └── nginx/
│       └── conf.d/
│           └── app.conf         # Nginx configuration
└── docker-compose.yml           # Docker Compose configuration
```

## 📦 Prerequisites

Before you begin, ensure you have the following installed:

- **Docker** (version 20.10+)
- **Docker Compose** (version 2.0+)
- **Composer** (for local development)
- **Node.js** (20.19.0+ or 22.12.0+)
- **NPM** (comes with Node.js)

## 🚀 Installation

### Option 1: Docker Setup (Recommended)

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd military-db
   ```

2. **Start Docker containers**
   ```bash
   docker-compose up -d
   ```

3. **Install PHP dependencies**
   ```bash
   docker-compose exec app composer install
   ```

4. **Copy environment file**
   ```bash
   docker-compose exec app cp .env.example .env
   ```

5. **Generate application key**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

6. **Run database migrations**
   ```bash
   docker-compose exec app php artisan migrate
   ```

7. **Seed the database (optional)**
   ```bash
   docker-compose exec app php artisan db:seed
   ```

8. **Install frontend dependencies**
   ```bash
   cd front
   npm install
   cd ..
   ```

9. **Build frontend assets**
   ```bash
   cd front
   npm run build
   cd ..
   ```

10. **Access the application**
    - Backend API: `http://localhost:8080`
    - Frontend (development): Run `cd front && npm run dev` (usually `http://localhost:5173`)
    - MySQL: `localhost:3306`

### Option 2: Local Development Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd military-db
   ```

2. **Backend setup**
   ```bash
   cd src
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   php artisan db:seed  # Optional
   ```

3. **Frontend setup**
   ```bash
   cd ../front
   npm install
   ```

4. **Start development servers**
   
   For backend (in `src/` directory):
   ```bash
   php artisan serve
   ```
   
   For frontend (in `front/` directory):
   ```bash
   npm run dev
   ```

## ⚙️ Configuration

### Environment Variables

Create a `.env` file in the `src/` directory with the following variables:

```env
APP_NAME="Military DB"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1  # Use 'db' for Docker setup
DB_PORT=3306
DB_DATABASE=military_db
DB_USERNAME=laravel
DB_PASSWORD=password

SANCTUM_STATEFUL_DOMAINS=localhost,localhost:5173,127.0.0.1

SESSION_DRIVER=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1  # Use 'redis' for Docker setup
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Frontend API Configuration

Update the API base URL in `front/src/stores/auth.js` if your backend runs on a different port:

```javascript
const response = await axios.post('http://localhost:8080/api/login', {
    email,
    password
})
```

## 💻 Usage

### Default Login Credentials

After seeding the database, you can create a user using Laravel Tinker:

```bash
docker-compose exec app php artisan tinker
```

Then create a user:
```php
User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password')
]);
```

### Main Features Usage

1. **Login**: Access the system using your credentials
2. **Dashboard**: View real-time statistics and overview
3. **Soldiers**: Manage personnel records, filter by various criteria
4. **Warehouse**: Track equipment inventory and status
5. **Assignments**: Issue and return equipment to soldiers
6. **Duty Roster**: Schedule and manage duty assignments
7. **Logs**: View system activity logs

## 🔌 API Endpoints

All API endpoints are prefixed with `/api` and require authentication (except login).

### Authentication
- `POST /api/login` - User login
- `POST /api/logout` - User logout (requires auth)

### Soldiers
- `GET /api/soldiers` - List all soldiers (with filters)
- `GET /api/soldiers/{id}` - Get soldier details
- `POST /api/soldiers` - Create new soldier
- `PUT /api/soldiers/{id}` - Update soldier
- `DELETE /api/soldiers/{id}` - Delete soldier

### Warehouse
- `GET /api/warehouse` - List all warehouse items
- `GET /api/warehouse/{id}` - Get warehouse item details
- `POST /api/warehouse` - Create new warehouse item
- `PUT /api/warehouse/{id}` - Update warehouse item
- `DELETE /api/warehouse/{id}` - Delete warehouse item

### Assignments
- `GET /api/assignments` - List all assignments
- `GET /api/assignments/active` - List active assignments
- `POST /api/assignments/issue` - Issue equipment to soldier
- `POST /api/assignments/return` - Return equipment

### Duty Roster
- `GET /api/roster` - Get duty roster
- `POST /api/roster` - Create duty assignment

### Dashboard & Logs
- `GET /api/stats` - Get dashboard statistics
- `GET /api/logs` - Get activity logs

## 🗄️ Database Schema

### Core Tables

- **users** - System users (authentication)
- **soldiers** - Personnel records (linked to ranks, units)
- **ranks** - Military ranks
- **units** - Military units
- **equipment_types** - Types of equipment
- **warehouses** - Equipment inventory
- **assignments** - Equipment assignment history
- **duty_types** - Types of duties
- **duty_rosters** - Duty schedule
- **logs** - Activity logs

### Relationships

- Soldier belongs to Rank and Unit
- Soldier has many Assignments and DutyRosters
- Warehouse belongs to EquipmentType
- Warehouse has many Assignments
- Assignment belongs to Soldier and Warehouse
- DutyRoster belongs to Soldier and DutyType

## 🔧 Development

### Code Style

The project uses Laravel Pint for code formatting:

```bash
docker-compose exec app ./vendor/bin/pint
```

### Laravel Telescope

Laravel Telescope is included for debugging. Access it at:
- `http://localhost:8080/telescope` (when running in Docker)

### Database Migrations

Create a new migration:
```bash
docker-compose exec app php artisan make:migration create_example_table
```

Run migrations:
```bash
docker-compose exec app php artisan migrate
```

Rollback migrations:
```bash
docker-compose exec app php artisan migrate:rollback
```

### Model Observers

The project uses Laravel Observers to automatically handle:
- Cache invalidation when soldiers/warehouse items change
- Logging of soldier creation/deletion
- Logging of warehouse status changes

## 🐛 Troubleshooting

### Docker Issues

1. **Port conflicts**: If port 8080 or 3306 is in use, modify `docker-compose.yml`
2. **Permission issues**: Ensure Docker has proper permissions
3. **Container not starting**: Check logs with `docker-compose logs`

### Database Connection Issues

1. Ensure MySQL container is running: `docker-compose ps`
2. Check database credentials in `.env`
3. Verify network connectivity: `docker-compose exec app ping db`

### Frontend Issues

1. Clear node_modules and reinstall: `rm -rf node_modules && npm install`
2. Check API URL configuration in auth store
3. Verify CORS settings in Laravel config

---

**Built with ❤️ using Laravel and Vue.js**

