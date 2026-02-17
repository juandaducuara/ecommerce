# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

E-commerce application built with **CodeIgniter 4.7+** and **PHP 8.2+**. Database is MySQL (configured as `ecommerce_db`). The app runs at `http://localhost:8080/` with timezone `America/Bogota`, currency COP, and 19% tax rate.

## Commands

```bash
# Install dependencies
composer install

# Start dev server (port 8080)
php spark serve

# Run all tests
composer test

# Run a specific test file
vendor/bin/phpunit tests/unit/HealthTest.php

# Database migrations
php spark migrate
php spark migrate:rollback
php spark migrate:refresh          # Reset and re-run all migrations
php spark migrate:refresh --seed   # Reset, re-run, and seed

# Seed database
php spark db:seed DatabaseSeeder   # Run all seeders
php spark db:seed ProductSeeder    # Run a specific seeder

# Generate scaffolding
php spark make:controller NameController
php spark make:model NameModel
php spark make:migration CreateTableName
```

## Architecture

The project follows a **layered/clean architecture** with four distinct layers:

### Domain Layer (`app/Domain/`)
Business entities organized by bounded context: `Catalog`, `Customer`, `Inventory`, `Sales`, `Shipping`. Pure domain logic, no framework dependencies.

### Application Layer (`app/Application/`)
- **DTOs/** — Data Transfer Objects for inter-layer communication (e.g., `ProductDTO`, `CartDTO`, `CheckoutDTO`), organized by domain
- **Services/** — Business logic orchestration
- **Validators/** — Validation rule definitions
- **Events/** — Domain event handlers

### Infrastructure Layer (`app/Infrastructure/`)
- **Persistence/Models/** — 18 CodeIgniter models (CI4's `Model` class with `$allowedFields`, timestamps, soft deletes)
- **Repositories/** — Data access abstraction over models
- **Payment/** — Payment gateway integrations (PayU, MercadoPago — both in sandbox mode)
- **Shipping/** — Shipping provider integrations
- **Listeners/** — Event listeners

### Presentation Layer (`app/Controllers/`)
Three controller groups:
- **Admin/** — Admin panel
- **Api/** — API v1 endpoints
- **Web/** — Frontend web controllers

Routes are defined in `app/Config/Routes.php`. Views live in `app/Views/`.

## Key Conventions

- **PSR-4 autoloading**: `App\` maps to `app/`, `Config\` maps to `app/Config/`
- **Database naming**: snake_case tables and columns; all tables have `created_at`/`updated_at` timestamps
- **Soft deletes**: Products and related entities use `deleted_at` columns
- **Foreign keys**: Explicit constraints with CASCADE/RESTRICT policies
- **Migration naming**: Sequential format `2024-01-01-000001_CreateTableName`
- **Mass assignment protection**: Models use `$allowedFields` arrays
- **Environment config**: `.env` file (copy from `env` template); key settings include `CI_ENVIRONMENT`, `app.baseURL`, and `database.default.*`
