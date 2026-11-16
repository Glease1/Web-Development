# Meridian Horizons

A travel agency website built from scratch with **PHP 8, SQLite, session-based basket and a role-based admin panel** — no framework, no Composer, no npm. It is a complete demonstration application, not a production service.

## Tech stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8.3 — front-controller routing, PDO, session management, CSRF protection |
| Database | SQLite 3 via PDO — zero-config, auto-created and seeded on first run |
| Frontend | Vanilla CSS design system (Playfair Display + Inter), vanilla JS price calculator |
| Deployment | PHP-FPM or another production PHP runtime behind a TLS reverse proxy |

## Getting started

Requires PHP 8.1+ with the `pdo_sqlite` extension (enabled by default in standard PHP builds).

```bash
git clone <your-repo-url>
cd Web-Development
php -S localhost:8080 -t public
```

Open **http://localhost:8080** — the SQLite database is created and seeded automatically on first request.

## Demo credentials

| Role  | Email               | Password |
|-------|---------------------|----------|
| Admin | admin@meridian.test | admin123 |
| User  | demo@meridian.test  | demo123  |

> Development-only seed accounts. Never deploy with these credentials.

## Features

### Customer-facing
- 26 destinations across all continents — Budget, Mid-Range, and Premium tiers
- Full booking flow: date picker, passenger selector, live JS price summary, group surcharge (6+ travellers), 10% deposit calculation
- Destination-aware upsell system — up to 8 per-destination add-ons including:
  - Private airport transfers
  - Room / suite / overwater villa upgrades (tiered by destination price)
  - Dining packages (breakfast through private chef)
  - Named destination excursions (e.g. Caldera catamaran for Santorini, marine package for Maldives)
  - Comprehensive travel insurance
  - Airport lounge access
  - Economy Plus or Business Class flight upgrades
  - Professional photography sessions (luxury tier)
- Session-based basket — works before login, merged on checkout
- "You May Also Like" destination recommendations
- Sticky footer with ABTA & ATOL branding
- FAQ, Terms, Privacy, Cookie Policy, Contact, About pages

### Admin panel
- **Booking dashboard**: all bookings with status filter tabs (All / Confirmed / Pending / Cancelled) and live search by customer or destination
- **Booking detail page**: destination hero image, full pricing breakdown, included highlights, customer panel, status update controls
- **User management**: create accounts, edit name/email/role, reset passwords, delete users — all with CSRF protection and self-modification guard
- **Destination management**: add, edit, and remove destinations

### Seed data
- 17 users with realistic staggered join dates (2023–2026)
- 20 bookings across Confirmed / Pending / Cancelled statuses

### Security
- CSRF token on every form
- XSS-safe output via `htmlspecialchars` (`h()` helper) on all user-supplied data
- Passwords hashed with `password_hash()` (bcrypt, cost 12)
- Admin routes guarded by role check

## Project structure

```
index.php                    Front controller — routing, auth guards, DB bootstrap
config/
  database.php               SQLite connection, auto-schema creation, seed data
src/
  TravelManager.php          Data-access layer — all DB queries
includes/
  functions.php              Helpers: h(), formatPrice(), CSRF, flash, redirect
  header.php
  footer.php                 Includes sticky footer bar
pages/
  home.php                   Homepage with featured destinations
  booking_details.php        Destination detail + booking form + upsell addons
  basket.php                 Basket review and checkout
  login.php / register.php
  admin_dashboard.php        Booking management overview
  admin_booking_detail.php   Full booking detail for admin
  admin_manage_listings.php  Destination CRUD
  admin_view_users.php       User management (create/edit/reset/delete)
  search_results.php
  about.php / contact.php / faq.php / terms.php / privacy.php / cookies.php
public/
  style.css                  Complete CSS design system (~2200 lines)
database/                    SQLite .db file (gitignored — auto-created on first run)
```

## Resetting the database

```bash
rm "database/meridian.db"
```

Reload the site — the database is recreated and re-seeded automatically.

## Deployment

Deploy the application with a production PHP runtime behind a TLS-terminating reverse
proxy. Configure the web server document root as `public/`; the application, SQLite
database, and configuration files must remain outside the publicly served directory.

## Production hardening notes

- Keep the web server document root set to `public/`; it must not serve the repository root
- Set `session.cookie_secure = 1` and `session.cookie_httponly = 1` in `php.ini`
- Replace the seed admin account with a proper setup CLI before any public-facing deployment
- Use a production web server (nginx + php-fpm) instead of the PHP built-in server

---

## Running it

Serve **`public/`** as the document root, never the project root:

```bash
php -S 127.0.0.1:8080 -t public
```

The SQLite database lives in `database/meridian.db`, which is **outside** `public/` and therefore not reachable over HTTP. Do not move it into the docroot.

## Security notes

- PDO prepared statements throughout; output escaped with `h()`; CSRF tokens on every POST.
- The admin area is gated by `requireAuth` + `requireRole('admin')`.
- The database, `config/`, `src/` and `includes/` sit outside the document root.
- Login throttling is **not** implemented, and demo accounts are seeded. Remove them before exposing this to anyone.

## Limitations

- A demonstration application, not a production service: `php -S` is a development server, there is no TLS, no rate limiting, no monitoring and no automated test suite.
- Contact, newsletter and forgot-password handlers flash a message rather than sending mail; they are UI placeholders.
- No framework experience is claimed from this project — it is deliberately framework-free.
