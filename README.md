# MOTOBOOK - Automobile Dealership Management System

> Built with **PHP 8.2**, **Slim Framework 4**, **Twig**, **Eloquent ORM**, and responsive **HTML5/CSS3/JavaScript**.

---

## Table of Contents
- [Project Overview](#project-overview)
- [Key Features](#key-features)
- [Technical Capabilities](#technical-capabilities)
- [Quick Start Guide](#quick-start-guide)
  - [Prerequisites](#prerequisites)
  - [Installation & Running](#installation--running)
- [Default Login Credentials](#default-login-credentials)
- [Database Setup](#database-setup)
- [Project Architecture](#project-architecture)

---

## Project Overview

**MOTOBOOK** is an automobile dealership and inventory management web application. It automates new and pre-owned vehicle acquisitions, inventory cataloging, customer inquiries, financial settlement accounting (cash, hire purchase, and installment bank loans), and administrative reporting.

---

## Key Features

1. **Executive Dashboard**:
   - Real-time sales metrics and Month-over-Month percentage tracking.
   - Financial summaries for direct and financed sales.
   - 15-day upcoming insurance expiration compliance alerts.

2. **Vehicle Inventory Management**:
   - **New Cars**: Docket tracking, RTO breakdown, accessories, ex-showroom pricing, and dealer margin calculations.
   - **Pre-Owned Cars**: Purchase ledger, refurbishment charges, RC Book, NOC, and Form 35 document management.

3. **Inquiry Pipeline (CRM)**:
   - Intake leads with contact preferences, vehicle of interest, and referral source.
   - Quick status filtering (`All`, `Pending`, `Completed`).

4. **Sales & Installment Accounting**:
   - Full payment milestone tracking.
   - Bank loan installment schedules with monthly parts and due dates.

5. **Security & Authentication**:
   - Session-based authentication with bcrypt password encryption.
   - Persistent login using Remember-Me cookies.

---

## Technical Capabilities

| Capability | Implementation in MOTOBOOK |
|---|---|
| **1. HTML5** | Semantic structure (`<main>`, `<aside>`, `<nav>`), responsive forms, custom data attributes |
| **2. CSS3** | Flexbox, CSS Grid layouts, CSS variables, mobile-responsive media queries (`css/app.css`) |
| **3. JavaScript** | DOM manipulation, dynamic event handling, form validation, Lucide icons, Notyf notifications |
| **4. AJAX/JSON/API** | Dynamic RESTful endpoints for search (`/api/cities`, `/api/manufacturers`, `/api/models`) |
| **5. Backend Development** | PHP 8.2 with Slim 4 micro-framework and PHP-DI dependency injection |
| **6. Database** | MySQL and SQLite supported via Eloquent ORM with full CRUD operations |
| **7. Authentication** | Bcrypt password hashing, session isolation, remember-me token handling |
| **8. Version Control** | Git and GitHub repository |
| **9. Testing & Quality** | Multi-tier test verification (Unit, Integration, Route, and Security checks) |
| **10. Deployment** | Ready for deployment on local development servers, XAMPP, or cloud hosting |

---

## Quick Start Guide

### Prerequisites
- **PHP 8.2+** (with `pdo_sqlite`, `pdo_mysql`, `curl`, `mbstring`, `fileinfo`, `zip` extensions enabled)
- **Composer 2.x**

### Installation & Running

1. **Clone the repository**:
   ```bash
   git clone https://github.com/fw10ishk/motobook.git
   cd motobook
   ```

2. **Install PHP dependencies**:
   ```bash
   cd app
   composer install
   cd ..
   ```

3. **Initialize the database**:
   ```bash
   php app/init_db.php
   ```

4. **Start the development server**:
   ```bash
   php -S localhost:8080
   ```

5. **Open in browser**:
   Navigate to **[http://localhost:8080](http://localhost:8080)**.

---

## Default Login Credentials

| Field | Value |
|---|---|
| **Login URL** | `http://localhost:8080` |
| **Email** | `admin@motobook.com` |
| **Password** | `admin123` |

---

## Database Setup

The project provides dual database compatibility:

### Option A: Portable SQLite (Zero Configuration - Default)
The repository is pre-configured to use SQLite out of the box via `app/database.sqlite`. Running `php app/init_db.php` automatically creates all 17 tables and seeds the admin user.

### Option B: MySQL / MariaDB (XAMPP)
1. Start MySQL in XAMPP.
2. Import the provided SQL dump file located at:
   ```
   database/motobook.sql
   ```
3. Update `app/.env`:
   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=motobook
   DB_USERNAME=root
   DB_PASSWORD=
   ```

---

## Project Architecture

```
motobook/
├── app/
│   ├── src/
│   │   ├── Controllers/     # Application controllers (User, Dashboard, Cars, Sales)
│   │   ├── Models/          # Eloquent ORM models (17 distinct entities)
│   │   ├── Middlewares/     # AuthMiddleware, Session, Flash guards
│   │   ├── Routes/          # web.php and api.php routing definitions
│   │   └── Helpers/         # File upload & utility helpers
│   ├── templates/           # Twig templates & component partials
│   ├── .env                 # Environment configuration
│   ├── .env.example         # Environment template
│   ├── bootstrap.php        # Eloquent Capsule & dual-DB initialization
│   ├── init_db.php          # Database migration & admin seeder
│   └── composer.json        # Dependencies specification
├── css/                     # Application stylesheets (app.css)
├── js/                      # Frontend JavaScript (app.js, table-filters.js)
├── database/
│   └── motobook.sql         # Complete MySQL database export
├── docs/
│   ├── PROJECT_REPORT.md    # Full 10-section Academic Project Report
│   └── Project_Report.pdf   # Formatted PDF Project Report for submission
├── images/                  # Graphic assets
├── uploads/                 # Uploaded vehicle photos and documents
├── index.php                # Application entry point
└── README.md                # Project documentation
```
