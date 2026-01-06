# Web Backend

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-purple.svg)](https://www.php.net/)
[![Slim 4](https://img.shields.io/badge/Slim-4.x-green.svg)](https://www.slimframework.com/)

The RESTful API backend for [api.nebalus.dev](https://api.nebalus.dev), built with PHP 8.3+ and the Slim 4 framework.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Requirements](#requirements)
- [Getting Started](#getting-started)
- [Configuration](#configuration)
- [API Endpoints](#api-endpoints)
- [Development Commands](#development-commands)
- [Project Structure](#project-structure)
- [Testing](#testing)
- [Code Quality](#code-quality)
- [Contributing](#contributing)
- [License](#license)

---

## Features

### Core Features
- **RESTful API** — Clean, modular architecture following REST principles
- **JWT Authentication** — Secure token-based authentication system
- **Role-Based Access Control (RBAC)** — Granular permission management with roles
- **Dependency Injection** — Full DI support via PHP-DI container
- **CORS Support** — Cross-Origin Resource Sharing middleware
- **Error Handling** — Centralized error handling with detailed logging

### Modules
- **Blog Module** — Create, edit, delete, and retrieve blog posts
- **Linktree Module** — Customizable link aggregation for user profiles with click tracking
- **Referral Module** — Referral link management with click analytics and history

### Admin Features
- **User Management** — Manage users and their role assignments
- **Role Management** — Create, edit, and delete roles
- **Permission Management** — Fine-grained permission control per role

### Operations
- **Health Checks** — Built-in `/health` endpoint for monitoring
- **Prometheus Metrics** — `/metrics` endpoint for observability
- **Structured Logging** — Monolog-based logging with configurable levels
- **Email Notifications** — Templated emails via Twig and Resend

---

## Tech Stack

| Category         | Technology                                                                 |
|------------------|---------------------------------------------------------------------------|
| **Language**     | PHP 8.3+                                                                   |
| **Framework**    | [Slim 4](https://www.slimframework.com/)                                   |
| **DI Container** | [PHP-DI](https://php-di.org/)                                              |
| **Database**     | MySQL with [Latitude](https://latitude.shadowhand.com/) query builder      |
| **Cache**        | Redis                                                                      |
| **Search**       | [Elasticsearch](https://www.elastic.co/elasticsearch/)                     |
| **Logging**      | [Monolog](https://seldaek.github.io/monolog/)                              |
| **Metrics**      | [Prometheus PHP Client](https://github.com/promphp/prometheus_client_php)  |
| **Authentication** | [ReallySimpleJWT](https://github.com/RobDWaller/ReallySimpleJWT)         |
| **2FA/OTP**      | [OTPHP](https://github.com/Spomky-Labs/otphp)                              |
| **Email**        | [Resend](https://resend.com/) + [Twig](https://twig.symfony.com/) templates|
| **HTTP Client**  | [Guzzle](https://docs.guzzlephp.org/)                                      |
| **Validation**   | [Sanitizr](https://github.com/nebalus/sanitizr)                            |
| **Testing**      | [PHPUnit 12](https://phpunit.de/)                                          |
| **Linting**      | [PHPMD](https://phpmd.org/) + [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) |
| **Containers**   | Docker + Docker Compose                                                    |
| **Task Runner**  | [Just](https://github.com/casey/just)                                      |

---

## Architecture

The application follows a clean, layered architecture pattern:

```
┌──────────────────────────────────────────────────────────────┐
│                         Middleware                           │
│  (CORS → Metrics → Auth → Permission → Body Parsing → Routing)│
└──────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌──────────────────────────────────────────────────────────────┐
│                          Actions                             │
│         (Handle HTTP requests, validate input)               │
└──────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌──────────────────────────────────────────────────────────────┐
│                         Services                             │
│              (Business logic and orchestration)              │
└──────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌──────────────────────────────────────────────────────────────┐
│                       Repositories                           │
│                (Data access and persistence)                 │
└──────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌──────────────────────────────────────────────────────────────┐
│                   MySQL / Redis / Elasticsearch              │
└──────────────────────────────────────────────────────────────┘
```

### Key Patterns

- **Action-Domain-Responder (ADR)** — Clean separation of HTTP handling from business logic
- **Value Objects** — Immutable domain objects for type safety
- **Repository Pattern** — Abstracted data access layer
- **Factory Pattern** — For creating complex objects (DI container, loggers, etc.)

### Middleware Stack

| Middleware               | Description                                      |
|-------------------------|--------------------------------------------------|
| `CorsMiddleware`        | Handles CORS headers and preflight requests      |
| `MetricsMiddleware`     | Collects Prometheus metrics for each request     |
| `AuthMiddleware`        | Validates JWT tokens and authenticates users     |
| `PermissionMiddleware`  | Enforces role-based permission checks            |

---

## Requirements

- **PHP 8.3+** with extensions: `pdo`, `redis`
- **Docker** & **Docker Compose**
- **[Just](https://github.com/casey/just)** — Command runner (recommended)
- **MySQL** — Running on an external Docker network
- **Composer** — PHP dependency manager

---

## Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/nebalus/WebBackend.git
cd WebBackend
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Configure Environment

Copy the example environment file and configure your secrets:

```bash
cp .env.example .env
```

Edit `.env` with your configuration:

```env
RESEND_API_KEY=YOUR_RESEND_API_KEY
JWT_SECRET=your_secure_random_jwt_secret
```

### 4. Set Up External Database Network

The application expects a MySQL database on an external Docker network:

```bash
docker network create nebalus-web-storage-development-net
```

> **Note:** Ensure your MySQL container is connected to this network.

### 5. Start the Development Environment

```bash
just start
```

Or using Docker Compose directly:

```bash
docker compose -f docker-compose.development.yml up
```

### 6. Access the API

The API will be available at **`http://localhost`**.

Test the health endpoint:

```bash
curl http://localhost/health
```

---

## Configuration

### Environment Variables

| Variable                     | Description                              | Default                |
|-----------------------------|------------------------------------------|------------------------|
| `APP_ENV`                   | Environment mode (`development`/`production`) | `development`      |
| `ERROR_LOGLEVEL`            | Monolog log level                        | `Warning`              |
| `JWT_SECRET`                | Secret key for JWT signing               | **Required**           |
| `JWT_EXPIRATION_TIME`       | JWT token expiration in seconds          | `86400` (24 hours)     |
| `ACCESS_CONTROL_ALLOW_ORIGIN` | CORS allowed origins                   | `*`                    |
| `MYSQL_HOST`                | MySQL hostname                           | `nebalus-web-storage-mysql` |
| `MYSQL_PORT`                | MySQL port                               | `3306`                 |
| `MYSQL_DATABASE`            | MySQL database name                      | `main`                 |
| `MYSQL_USER`                | MySQL username                           | `admin`                |
| `MYSQL_PASSWORD`            | MySQL password                           | **Required**           |
| `REDIS_HOST`                | Redis hostname                           | `redis`                |
| `REDIS_PORT`                | Redis port                               | `6379`                 |
| `RESEND_API_KEY`            | Resend API key for email delivery        | **Required**           |

### Configuration Files

| File           | Purpose                                    |
|----------------|--------------------------------------------|
| `phpunit.xml`  | PHPUnit test configuration                 |
| `phpcs.xml`    | PHP_CodeSniffer code style rules           |
| `phpmd.xml`    | PHPMD mess detector rules                  |

---

## API Endpoints

### Public Endpoints

| Method | Endpoint                          | Description                    |
|--------|----------------------------------|--------------------------------|
| `GET`  | `/health`                        | Health check                    |
| `GET`  | `/metrics`                       | Prometheus metrics              |
| `GET`  | `/services/referral/{code}`      | Track referral link click       |
| `GET`  | `/services/linktree/{username}`  | Get user's linktree             |

### Authentication

| Method | Endpoint          | Description                          |
|--------|------------------|--------------------------------------|
| `POST` | `/ui/auth`       | Authenticate and receive JWT token   |
| `POST` | `/ui/register`   | Register a new user                  |

### User Services (Authenticated)

Base path: `/ui/users/{user_id}/services`

#### Blogs

| Method   | Endpoint                    | Description              |
|----------|----------------------------|--------------------------|
| `POST`   | `/blogs`                   | Create a new blog post   |
| `GET`    | `/blogs/all`               | Get all user's blogs     |
| `GET`    | `/blogs/{blog_id}`         | Get a specific blog      |
| `PUT`    | `/blogs/{blog_id}`         | Update a blog            |
| `DELETE` | `/blogs/{blog_id}`         | Delete a blog            |

#### Linktree

| Method   | Endpoint        | Description                    |
|----------|----------------|--------------------------------|
| `GET`    | `/linktree`    | Get user's linktree config     |
| `PUT`    | `/linktree`    | Update linktree                |
| `DELETE` | `/linktree`    | Delete linktree                |

#### Referrals

| Method   | Endpoint                              | Description                      |
|----------|---------------------------------------|----------------------------------|
| `POST`   | `/referrals`                          | Create a new referral            |
| `GET`    | `/referrals/all`                      | Get all user's referrals         |
| `GET`    | `/referrals/{code}`                   | Get a specific referral          |
| `PUT`    | `/referrals/{code}`                   | Update a referral                |
| `DELETE` | `/referrals/{code}`                   | Delete a referral                |
| `GET`    | `/referrals/{code}/click_history`     | Get referral click history       |

### Admin Endpoints (Authenticated + Permissions Required)

Base path: `/ui/admin`

#### Permissions

| Method | Endpoint                        | Description             |
|--------|--------------------------------|-------------------------|
| `GET`  | `/permissions/all`             | List all permissions    |
| `GET`  | `/permissions/{permission_id}` | Get a specific permission |

#### Roles

| Method   | Endpoint                                   | Description                    |
|----------|-------------------------------------------|--------------------------------|
| `POST`   | `/roles`                                  | Create a new role              |
| `GET`    | `/roles/all`                              | List all roles                 |
| `GET`    | `/roles/{role_id}`                        | Get a specific role            |
| `PUT`    | `/roles/{role_id}`                        | Update a role                  |
| `DELETE` | `/roles/{role_id}`                        | Delete a role                  |
| `GET`    | `/roles/{role_id}/permissions/all`        | Get role's permissions         |
| `PUT`    | `/roles/{role_id}/permissions`            | Upsert role permissions        |
| `DELETE` | `/roles/{role_id}/permissions`            | Delete role permissions        |

#### User Role Management

| Method   | Endpoint                            | Description                 |
|----------|------------------------------------|-----------------------------|
| `GET`    | `/users/{user_id}/roles/all`       | Get user's roles            |
| `POST`   | `/users/{user_id}/roles/{role_id}` | Assign role to user         |
| `DELETE` | `/users/{user_id}/roles/{role_id}` | Remove role from user       |

---

## Development Commands

The project uses [Just](https://github.com/casey/just) as a command runner:

### Development

| Command        | Description                              |
|----------------|------------------------------------------|
| `just start`   | Start development containers             |
| `just stop`    | Stop development containers              |
| `just restart` | Restart development containers           |
| `just build`   | Build development containers             |
| `just test`    | Run PHPUnit tests                        |
| `just lint`    | Run PHPMD and PHP_CodeSniffer            |

### Production

| Command             | Description                          |
|---------------------|--------------------------------------|
| `just start-prod`   | Start production containers          |
| `just stop-prod`    | Stop production containers           |
| `just restart-prod` | Restart production containers        |
| `just build-prod`   | Build production containers          |

---

## Project Structure

```
.
├── docker/                           # Docker configuration
│   ├── nginx/                        # Nginx Dockerfile and config
│   ├── php-fpm/                      # PHP-FPM Dockerfile and config
│   └── redis/                        # Redis Dockerfile
├── endpoints/                        # Bruno API collection for testing
│   ├── Services/                     # Service endpoint definitions
│   ├── Ui/                           # UI endpoint definitions
│   └── environments/                 # Environment configs
├── public/                           # Web root
│   └── index.php                     # Application entry point
├── src/                              # Application source code
│   ├── Api/                          # API layer
│   │   ├── AbstractAction.php        # Base action class
│   │   ├── AbstractService.php       # Base service class
│   │   ├── AbstractValidator.php     # Base validator class
│   │   ├── Admin/                    # Admin endpoints
│   │   │   ├── Permission/           # Permission management
│   │   │   ├── Role/                 # Role management
│   │   │   └── User/                 # User management
│   │   ├── Health/                   # Health check endpoint
│   │   ├── Metrics/                  # Prometheus metrics endpoint
│   │   ├── Module/                   # Feature modules
│   │   │   ├── Blog/                 # Blog CRUD operations
│   │   │   ├── Linktree/             # Linktree management
│   │   │   └── Referral/             # Referral tracking
│   │   └── User/                     # User authentication
│   │       ├── Auth/                 # Login/token generation
│   │       ├── GetUserPermissions/   # Permission retrieval
│   │       └── Register/             # User registration
│   ├── Config/                       # Configuration classes
│   │   ├── GeneralConfig.php         # General app settings
│   │   ├── MySqlConfig.php           # MySQL connection config
│   │   ├── RedisConfig.php           # Redis connection config
│   │   └── ResendConfig.php          # Email service config
│   ├── Exception/                    # Custom exceptions
│   ├── Factory/                      # Factory classes
│   ├── Repository/                   # Data access layer
│   │   ├── AccountRepository/        # Account data access
│   │   ├── BlogRepository/           # Blog data access
│   │   ├── LinktreeRepository/       # Linktree data access
│   │   ├── PermissionsRepository/    # Permission data access
│   │   ├── ReferralRepository/       # Referral data access
│   │   ├── RoleRepository/           # Role data access
│   │   └── UserRepository/           # User data access
│   ├── Slim/                         # Slim framework extensions
│   │   ├── Handler/                  # Error handlers
│   │   ├── Middleware/               # Custom middleware
│   │   │   ├── AuthMiddleware.php    # JWT authentication
│   │   │   ├── CorsMiddleware.php    # CORS handling
│   │   │   ├── MetricsMiddleware.php # Prometheus metrics
│   │   │   └── PermissionMiddleware.php # RBAC enforcement
│   │   └── RouteCollector.php        # Route definitions
│   ├── Utils/                        # Utility classes
│   └── Value/                        # Value objects
├── templates/                        # Twig templates
│   └── email/                        # Email templates
├── tests/                            # PHPUnit tests
│   └── Api/                          # API tests (mirrors src/Api)
├── .env.example                      # Environment template
├── composer.json                     # PHP dependencies
├── docker-compose.development.yml    # Dev Docker Compose
├── justfile                          # Just command definitions
├── phpcs.xml                         # Code style config
├── phpmd.xml                         # PHPMD config
└── phpunit.xml                       # PHPUnit config
```

---

## Testing

### Running Tests

Run the full test suite:

```bash
just test
```

Or directly via Docker:

```bash
docker compose -f docker-compose.development.yml run php-fpm \
  /var/www/vendor/bin/phpunit -c /var/www/phpunit.xml
```

### Test Structure

Tests are organized to mirror the `src/` directory structure:

```
tests/
└── Api/
    ├── Admin/
    │   ├── Permission/
    │   ├── Role/
    │   └── User/
    ├── Module/
    │   ├── Blog/
    │   ├── Linktree/
    │   └── Referral/
    └── User/
```

Each endpoint typically has tests for:
- `*ActionTest.php` — HTTP action tests
- `*ServiceTest.php` — Business logic tests
- `*ValidatorTest.php` — Input validation tests
- `*ResponderTest.php` — Response formatting tests

---

## Code Quality

### Running Linters

Run all linting checks:

```bash
just lint
```

This executes:

1. **PHPMD** (PHP Mess Detector):
   ```bash
   phpmd src text phpmd.xml
   ```

2. **PHP_CodeSniffer**:
   ```bash
   phpcs --standard=phpcs.xml src
   ```

### Code Style

The project enforces consistent code style through:

- **PHPMD** — Detects potential problems, unused code, and complexity issues
- **PHP_CodeSniffer** — Enforces PSR-12 coding standards

---

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Make your changes
4. Run tests: `just test`
5. Run linting: `just lint`
6. Commit your changes: `git commit -m 'Add my feature'`
7. Push to the branch: `git push origin feature/my-feature`
8. Open a Pull Request

### Code Standards

- Follow PSR-12 coding standards
- Write tests for new features
- Ensure all tests pass before submitting
- Document public APIs

---

## License

This project is licensed under the **MIT License** — see the [LICENSE](LICENSE) file for details.

---

## Author

**Nebalus**  
📧 [coding@nebalus.dev](mailto:coding@nebalus.dev)  
🌐 [nebalus.dev](https://nebalus.dev)
