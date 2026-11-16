# Meridian Horizons

A framework-free travel booking application — PHP 8.3, SQLite, session-based basket, role-based admin, and a complete demo booking flow. Zero external dependencies: no framework, no Composer, no npm. It is a **development/demo application**, not a production travel agency.

## Tech stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8.3 — front-controller routing, PDO, session management, CSRF protection |
| Database | SQLite 3 via PDO — zero-config, auto-created and seeded on first run |
| Frontend | Vanilla CSS design system (Playfair Display + Inter), vanilla JS price calculator |
| Deployment | PHP's built-in server for local use; no production deployment is included |

## Getting started

Requires PHP 8.1+ with the `pdo_sqlite` extension (enabled by default in standard PHP builds). `mbstring` is optional; the one-letter avatar uses a fallback.

```bash
cd Web-Development
php -S localhost:8080 -t public
```

Open **http://localhost:8080**. The SQLite database is created outside the web root at `../meridian-data/meridian.db` and seeded on first request. Override the location with `MERIDIAN_DB_PATH`:

```bash
MERIDIAN_DB_PATH=/var/lib/meridian/meridian.db php -S localhost:8080 -t public
```

**Do not serve the project root.** The `public/` directory is the document root. Serving the project root exposes `config/`, `src/`, `includes/`, and the legacy `database/` directory.

## Demo credentials

| Role  | Email               | Password |
|-------|---------------------|----------|
| Admin (demo only) | admin@meridian.test | admin123 |
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
- Session-based basket — works before login; checkout requires an account so a booking is never silently discarded
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
- Database and application config live outside `public/`
- Session cookies use `HttpOnly`, `SameSite=Lax`, and `Secure` when HTTPS is detected (or `MERIDIAN_COOKIE_SECURE=1`)

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
  index.php                  Public front controller (requires ../index.php)
  style.css                  Complete CSS design system (~2200 lines)
database/                    Legacy location only; .htaccess denies it if the root is ever exposed
../meridian-data/            SQLite database outside the web root (created on first run)
```

## Resetting the database

Stop the service, remove the database at `../meridian-data/meridian.db` (or the path in `MERIDIAN_DB_PATH`), then reload. The schema and demo data are recreated on the next request.

```bash
rm "../meridian-data/meridian.db"
```

## Deployment

There is no deployment automation in this repository. The role installs PHP 8.3, creates a `/opt/meridian` symlink, creates `~/meridian-data`, and manages a systemd service (`meridian`) that serves `public/` on port 8080 with `MERIDIAN_DB_PATH` pointing outside the web root.

The only automated check is `.github/workflows/php.yml`, which lints every PHP file on push.

## Production hardening notes

- The database is outside the document root by default; keep it there.
- Set `MERIDIAN_COOKIE_SECURE=1` when serving over HTTPS (the app auto-detects direct HTTPS).
- Replace the seed admin account with a proper setup CLI before any public-facing deployment.
- Move from `php -S` to nginx + PHP-FPM with `public/` as the document root; the built-in server is for local development only.
- Add login rate limiting and a backup/restore process before exposing the app.

## Demo behaviour that is not implemented

- **Password reset is simulated.** The page validates the address and flashes a confirmation; it issues no token and sends no message. There is no mail transport in this project.
- **"Keep me signed in"** is a visual control only — there is no persistent-login cookie.
- **Demo accounts are seeded** (`admin@meridian.test` / `admin123`) so the admin panel can be reviewed. Remove them, or change the password, before hosting this anywhere.
