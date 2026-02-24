# 🛡️ Military Database Management System

A comprehensive web application for managing military personnel, equipment, assignments, and duty rosters. Built with a **Laravel** backend, **Vue.js** frontend, and a microservice architecture powered by **RabbitMQ**, **Elasticsearch**, **Kibana**, and a dedicated **Go** PDF-generation service.

## 📋 Table of Contents

- [Overview](#-overview)
- [Tech Stack](#-tech-stack)
- [Architecture](#-architecture)
- [Features](#-features)
- [Project Structure](#-project-structure)
- [Prerequisites](#-prerequisites)
- [Installation](#-installation)
- [CI/CD Pipeline](#-cicd-pipeline)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [API Endpoints](#-api-endpoints)
- [Database Schema](#-database-schema)
- [Development](#-development)
- [Troubleshooting](#-troubleshooting)

---

## 🎯 Overview

This Military Database Management System is designed to streamline the administration of military units. It provides:

- **Personnel Management** — Track soldiers with ranks, units, and statuses
- **Equipment Management** — Manage warehouse inventory and equipment assignments
- **Duty Roster Management** — Schedule and track duty assignments
- **Activity Logging** — Centralized logging microservice consuming events from RabbitMQ
- **Dashboard Analytics** — Real-time statistics and overview
- **PDF Report Generation** — Export data tables as PDF files via a dedicated Go microservice
- **Full-Text Search** — Fuzzy search powered by Elasticsearch
- **Log Visualization** — Kibana dashboard for exploring and visualizing system logs

---

## 🛠️ Tech Stack

### Core Backend
| Technology | Purpose |
|---|---|
| **PHP 8.2+** | Server-side language |
| **Laravel 12** | Main PHP framework |
| **Laravel Sanctum** | API authentication |
| **Laravel Scout** | Full-text search abstraction |
| **Laravel Telescope** | Debugging & monitoring |
| **Laravel Pint** | Code style enforcement (PSR-12) |
| **PHPUnit** | Automated testing |

### Microservices
| Service | Language / Framework | Role |
|---|---|---|
| **Logger Service** | PHP / Laravel | Consumes RabbitMQ events, saves logs, indexes to Elasticsearch |
| **Kibana Proxy Service** | PHP / Symfony | Bridges the main app with Kibana, handles log analysis views |
| **PDF Report Service** | **Go** + gRPC | Converts data tables to PDF and streams them to the user |

### Infrastructure & DevOps
| Technology | Purpose |
|---|---|
| **Docker & Docker Compose** | Full containerized environment |
| **Nginx** | Web server / reverse proxy |
| **MySQL 8.0** | Primary relational database |
| **Redis** | Caching, sessions, and queue driver |
| **RabbitMQ** | Message broker for async inter-service communication & queues |
| **Elasticsearch 8.11** | Distributed search and analytics engine |
| **Kibana 8.11** | Log visualization & analytics UI |
| **Supervisor** | Process manager — auto-starts queue workers inside containers |
| **GitHub Actions** | CI/CD pipeline (Pint + PHPUnit on every push) |

### Frontend
| Technology | Purpose |
|---|---|
| **Vue.js 3** | Progressive JavaScript framework |
| **Vue Router 4** | Client-side routing |
| **Pinia** | State management |
| **Axios** | HTTP client |
| **Vite** | Build tool and dev server |

---

## 🏗️ Architecture

```
┌───────────────────────┐     ┌──────────────────────────┐
│   Vue.js Frontend     │────▶│   Laravel App (Core)     │
│   :5173               │     │   :8080  (Nginx + FPM)   │
└───────────────────────┘     └──────────┬───────────────┘
                                         │ RabbitMQ events
                              ┌──────────▼───────────────┐
                              │        RabbitMQ           │
                              │        :5672 / :15672     │
                              └──┬─────────────┬─────────┘
                                 │             │
                    ┌────────────▼──┐   ┌──────▼──────────────┐
                    │ Logger Service│   │ Symfony / Kibana Svc │
                    │ :8001         │   │ :8002                │
                    └────────┬──────┘   └──────────────────────┘
                             │ indexes logs
                    ┌────────▼──────┐     ┌──────────────────┐
                    │ Elasticsearch │────▶│    Kibana         │
                    │ :9200         │     │    :5601          │
                    └───────────────┘     └──────────────────┘

                    ┌───────────────────────┐
                    │   Go PDF Service      │  ← gRPC
                    │   (Report generation) │
                    └───────────────────────┘
```

### Auto-Start via Supervisor & Entrypoint

Each container's `entrypoint.sh` waits for **MySQL** and **RabbitMQ** to be ready before it starts. Depending on `SERVICE_ROLE`:

| Role | Actions on startup |
|---|---|
| `core` | Runs `migrate:fresh --seed`, imports models to Elasticsearch, starts Supervisor (php-fpm + queue worker) |
| `logger` | Runs `migrate:fresh`, imports logs to Elasticsearch, starts Supervisor (php-fpm + queue worker) |
| `symfony` | Runs Doctrine migrations, starts Supervisor (php-fpm + Symfony Messenger worker) |

No manual `php artisan queue:work` needed — **Supervisor manages everything automatically**.

---

## ✨ Features

### 1. Personnel Management (Soldiers)
- Full CRUD for soldier records
- Filter by unit, rank, and status
- **Fuzzy search** with typo tolerance powered by Elasticsearch

### 2. Warehouse Management
- Equipment inventory tracking
- **Smart search** by serial number with wildcard support
- Status tracking: `in_stock`, `issued`, `broken`

### 3. Equipment Assignments
- Issue and return equipment
- Automatic status updates via Service Layer & Observers
- Full assignment history

### 4. Duty Roster
- Schedule soldiers for different duty types
- Track duty periods with start and end times

### 5. Dashboard & Statistics
- Redis-cached real-time statistics
- Soldier counts, equipment availability, active duties

### 6. Activity Logging (via RabbitMQ + Logger Microservice)
- All critical actions publish events to RabbitMQ
- The **Logger Service** consumes these events asynchronously and persists them
- Logs are indexed to **Elasticsearch** for fast retrieval
- Visualize logs in **Kibana** at `http://localhost:5601`

### 7. PDF Report Generation (Go Microservice)
- The dedicated **Go service** receives report requests via **gRPC**
- Converts data tables into a formatted **PDF** and delivers it to the user for download
- Completely independent service — no PHP involved in generation

### 8. Kibana / Symfony Service
- A **Symfony** microservice bridges the main app with Kibana
- Provides a clean API layer for log queries and analytics

### 9. Authentication & Security
- Laravel Sanctum token-based authentication
- Protected API routes with Bearer token

---

## 📁 Project Structure

```
military-db/
├── src/                          # Laravel backend (core)
│   ├── app/
│   │   ├── Http/Controllers/     # API controllers
│   │   ├── Http/Requests/        # Form request validation
│   │   ├── Http/Resources/       # API resources
│   │   ├── Models/               # Eloquent models
│   │   ├── Observers/            # Model observers (cache + logging)
│   │   ├── Providers/            # Service providers
│   │   └── Services/             # Business logic services
│   ├── database/
│   │   ├── migrations/           # Database migrations
│   │   ├── seeders/              # Database seeders
│   │   └── factories/            # Model factories
│   ├── routes/api.php            # API routes
│   └── tests/
│       └── Feature/              # Feature tests
│           ├── SoldierTest.php
│           ├── WarehouseTest.php
│           ├── AssignmentTest.php
│           ├── RosterTest.php
│           └── LoggerTest.php
├── front/                        # Vue.js frontend
│   └── src/
│       ├── views/                # Vue pages
│       ├── router/               # Vue Router config
│       ├── stores/               # Pinia stores
│       └── App.vue
├── services/
│   ├── go/                       # Go PDF microservice (gRPC)
│   │   └── pb/                   # Protobuf generated files
│   ├── logger/                   # Laravel Logger microservice
│   └── symfony/                  # Symfony Kibana-bridge microservice
├── docker/
│   ├── php/
│   │   ├── Dockerfile            # Shared PHP-FPM image
│   │   ├── entrypoint.sh         # Smart entrypoint (waits for deps, runs setup)
│   │   ├── supervisord.conf      # Supervisor config (core + logger)
│   │   └── supervisord-symfony.conf  # Supervisor config (symfony)
│   ├── nginx/conf.d/
│   │   ├── app.conf              # Core app Nginx config
│   │   ├── logger.conf           # Logger service Nginx config
│   │   └── symfony.conf          # Symfony service Nginx config
│   └── mysql/init.sql            # MySQL init script
├── .github/workflows/ci.yml      # GitHub Actions CI pipeline
└── docker-compose.yml            # Full stack orchestration
```

---

## 📦 Prerequisites

- **Docker** (20.10+)
- **Docker Compose** (2.0+)

That's it. Everything else runs inside Docker.

---

## 🚀 Installation

### Quick Start (Docker — Recommended)

1. **Clone the repository**
   ```bash
   git clone [<repository-url>](https://github.com/NikitaZushchyk/military-db)
   cd military-db
   ```

2. **Copy the environment file**
   ```bash
   cp src/.env.example src/.env
   ```
   ```bash
   cp services/logger/.env.example services/logger/.env
   ```

3. **Start all services**
   ```bash
   docker-compose up -d --build
   ```

4. **Generate Application Keys**
   ```bash
   docker-compose exec app php artisan key:generate
   ```
   ```bash
   docker-compose exec service_logger php artisan key:generate
   ```

   > All migrations, seeds, Elasticsearch indexing, and queue workers start **automatically** via `entrypoint.sh` and Supervisor. No manual steps required.

4. **Access the application**

   | Service | URL |
   |---|---|
   | Backend API | http://localhost:8080 |
   | Frontend (Vue.js) | http://localhost:5173 |
   | Logger Service | http://localhost:8001 |
   | Symfony/Kibana Service | http://localhost:8002 |
   | Kibana | http://localhost:5601 |
   | RabbitMQ Management | http://localhost:15672 |
   | Elasticsearch | http://localhost:9200 |
   | Laravel Telescope | http://localhost:8080/telescope |

5. **Create an admin user**
   ```bash
   docker-compose exec app php artisan tinker
   ```
   ```php
   User::create([
       'name'     => 'Admin',
       'email'    => 'admin@example.com',
       'password' => bcrypt('password'),
   ]);
   ```

### Useful Docker Commands

```bash
# View all running containers
docker-compose ps

# Follow logs for all services
docker-compose logs -f

# Follow logs for a specific service
docker-compose logs -f app
docker-compose logs -f service_logger

# Restart a single service
docker-compose restart app

# Stop everything
docker-compose down

# Stop and wipe all volumes (fresh start)
docker-compose down -v
```

---

## 🔄 CI/CD Pipeline

The project uses **GitHub Actions** (`.github/workflows/ci.yml`) with a two-job pipeline triggered on every **push** and **pull request**:

### Job 1: Pint (Code Style)
- Runs **Laravel Pint** to auto-fix code style (PSR-12)
- Automatically **commits** any style fixes back to the branch using `git-auto-commit-action`

### Job 2: Tests (runs after Pint)
- Spins up **MySQL 8.0** and **RabbitMQ** as GitHub Actions services
- Runs database migrations on a test database
- Executes the full **PHPUnit** test suite (`php artisan test`)
- Tests covered:
  - `SoldierTest` — soldier CRUD & search
  - `WarehouseTest` — warehouse CRUD
  - `AssignmentTest` — equipment issue/return
  - `RosterTest` — duty roster management
  - `LoggerTest` — activity logging via RabbitMQ

---

## ⚙️ Configuration

### Environment Variables (`src/.env`)

```env
APP_NAME="Military DB"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

APP_LOCALE=uk
APP_FALLBACK_LOCALE=en

# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=military_db
DB_USERNAME=laravel
DB_PASSWORD=password

# Redis — used for cache, sessions, and queue driver
SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=rabbitmq

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# RabbitMQ — message broker for inter-service communication
RABBITMQ_HOST=rabbitmq
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest

# Elasticsearch
SCOUT_DRIVER=Matchish\ScoutElasticSearch\Engines\ElasticSearchEngine
SCOUT_QUEUE=true
ELASTICSEARCH_HOST=elasticsearch:9200

# Mail (for testing)
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

---

## 💻 Usage

### Main Features

1. **Login** — Authenticate via the Vue.js frontend
2. **Dashboard** — View real-time cached statistics
3. **Soldiers** — Full personnel management with fuzzy search
4. **Warehouse** — Equipment inventory with smart search
5. **Assignments** — Issue and return equipment to soldiers
6. **Duty Roster** — Schedule duty assignments
7. **Logs** — Browse system activity logs
8. **PDF Export** — Generate and download data reports as PDF files (via Go service)
9. **Kibana** — Explore and visualize system logs at `http://localhost:5601`

---

## 🔌 API Endpoints

All endpoints are prefixed with `/api` and require authentication (except login).

### Authentication
| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/login` | User login |
| `POST` | `/api/logout` | User logout |

### Soldiers
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/soldiers` | List all soldiers (filterable) |
| `GET` | `/api/soldiers/pdfExport` | Export soldiers data to PDF |
| `GET` | `/api/soldiers/{id}` | Get soldier details |
| `POST` | `/api/soldiers` | Create a soldier |
| `PUT` | `/api/soldiers/{id}` | Update a soldier |
| `DELETE` | `/api/soldiers/{id}` | Delete a soldier |

### Warehouse
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/warehouse` | List all warehouse items |
| `GET` | `/api/warehouse/pdfExport` | Export equipment data to PDF |
| `GET` | `/api/warehouse/{id}` | Get item details |
| `POST` | `/api/warehouse` | Create an item |
| `PUT` | `/api/warehouse/{id}` | Update an item |
| `DELETE` | `/api/warehouse/{id}` | Delete an item |

### Assignments
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/assignments` | List all assignments |
| `GET` | `/api/assignments/pdfExport` | Export assignments history to PDF |
| `GET` | `/api/assignments/active` | List active assignments |
| `POST` | `/api/assignments/issue` | Issue equipment to soldier |
| `POST` | `/api/assignments/return` | Return equipment |

### Duty Roster
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/roster` | Get duty roster |
| `GET` | `/api/roster/pdfExport` | Export duty roster to PDF |
| `POST` | `/api/roster` | Create duty assignment |

### Dashboard & Logs
| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/stats` | Dashboard statistics |
| `GET` | `/api/logs` | Activity logs |

---

## 🗄️ Database Schema

### Core Tables (MySQL)

| Table | Description |
|---|---|
| `users` | System users (authentication) |
| `soldiers` | Personnel records |
| `ranks` | Military ranks |
| `units` | Military units |
| `equipment_types` | Types of equipment |
| `warehouses` | Equipment inventory |
| `assignments` | Equipment assignment history |
| `duty_types` | Types of duties |
| `duty_rosters` | Duty schedule |
| `logs` | Activity logs |

### Relationships
- **Soldier** belongs to Rank and Unit; has many Assignments and DutyRosters
- **Warehouse** belongs to EquipmentType; has many Assignments
- **Assignment** belongs to Soldier and Warehouse
- **DutyRoster** belongs to Soldier and DutyType

---

## 🔧 Development

### Code Style (Laravel Pint)

```bash
# Auto-fix code style (PSR-12)
docker-compose exec app ./vendor/bin/pint

# Check without fixing
docker-compose exec app ./vendor/bin/pint --test
```

### Running Tests

```bash
docker-compose exec app php artisan test
```

### Database Management

```bash
# Fresh migration + seed
docker-compose exec app php artisan migrate:fresh --seed

# Create a migration
docker-compose exec app php artisan make:migration create_example_table

# Rollback
docker-compose exec app php artisan migrate:rollback
```

### Queue Management

> **Note:** Queue workers are managed by Supervisor and start automatically. You normally don't need to run these manually.

```bash
# Check worker status via Supervisor
docker-compose exec app supervisorctl status

# Manually restart worker
docker-compose exec app supervisorctl restart laravel-worker

# Monitor queued jobs
docker-compose exec app php artisan queue:monitor
```

### Laravel Telescope

Access Telescope at `http://localhost:8080/telescope` to inspect:
- HTTP requests
- Database queries
- Queue jobs
- Cache operations
- RabbitMQ events

---

## 🐛 Troubleshooting

### Containers not starting
```bash
# Check logs
docker-compose logs app
docker-compose logs service_logger
docker-compose logs service_symfony
```

### Port conflicts
Modify port mappings in `docker-compose.yml` if any of these ports are in use:
- `8080` (core app), `5173` (frontend), `8001` (logger), `8002` (symfony)
- `5601` (Kibana), `9200` (Elasticsearch), `5672`/`15672` (RabbitMQ), `3306` (MySQL)

### Database connection issues
```bash
docker-compose exec app ping db
docker-compose exec app php artisan migrate:status
```

### RabbitMQ issues
Access the RabbitMQ management UI at `http://localhost:15672` (default credentials: `guest`/`guest`) to inspect queues and connections.

### Elasticsearch issues
```bash
# Check cluster health
curl http://localhost:9200/_cluster/health

# Re-import data
docker-compose exec app php artisan scout:import "App\Models\Soldier"
docker-compose exec app php artisan scout:import "App\Models\Warehouse"
```

### Frontend issues
```bash
docker-compose logs front
# Or rebuild
docker-compose up -d --build front
```

---

**Built with ❤️ using Laravel, Vue.js, Go, Symfony, RabbitMQ, Elasticsearch & Docker**
