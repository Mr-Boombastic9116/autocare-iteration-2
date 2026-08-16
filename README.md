# AutoCare — Vehicle Management & Service Booking

A PHP + MySQL vehicle management and service booking system. Add your
vehicles, track their health/service status, and book a service slot
with real-time slot availability.

## Requirements

- XAMPP (Apache + MySQL/MariaDB + PHP 8.x)
- phpMyAdmin (bundled with XAMPP)

No Composer, Node.js, or external frameworks are required — this runs
as plain PHP on Apache.

## Installation

1. Copy the `autocare-main` folder into your XAMPP `htdocs/` directory, e.g.:
   ```
   C:\xampp\htdocs\autocare-main\      (Windows)
   /Applications/XAMPP/htdocs/autocare-main/   (macOS)
   /opt/lampp/htdocs/autocare-main/    (Linux)
   ```

2. Start **Apache** and **MySQL** from the XAMPP control panel.

3. Open phpMyAdmin: `http://localhost/phpmyadmin`

4. Create a new database named exactly:
   ```
   autocare
   ```

5. Select the `autocare` database, go to **Import**, and import:
   ```
   databases/autocare_final.sql
   ```
   This is the single, consolidated database file — it contains the
   full schema (users, vehicles, bookings, companies, models, years,
   fuels, variants) plus starter demo data. The two older SQL dumps
   (`autocare old.sql`, `autocare updated.sql`) are kept in the same
   folder only for reference; you do **not** need to import them.

6. Open the app in your browser:
   ```
   http://localhost/autocare-main/
   ```

## Default / demo login

The imported database includes one demo account:

- **Username:** `admin`
- **Password:** `Admin@123`

You can also sign up a brand-new account from the Signup page — new
accounts start with zero vehicles and zero bookings.

## Database connection

`includes/db.php` connects using the standard local XAMPP defaults:

```php
new mysqli("localhost", "root", "", "autocare");
```

If your local MySQL root user has a password set, update that one
file only.

## What's in this build

- **Per-user vehicles** — every user only ever sees and manages their
  own vehicles; there is no shared/hardcoded vehicle data.
- **Real booking flow** — Login → My Vehicles → Vehicle Details →
  Book Service → Date → Time → Services → Confirmation, all tied to
  the vehicle you actually selected.
- **Server-verified slot availability** — the same date/time slot can
  never be double-booked, checked both in the booking transaction and
  at the database level (`UNIQUE(service_date, time_slot)`).
- **Ownership checks everywhere** — vehicle details, booking, and
  confirmation pages all verify server-side that the record belongs
  to the logged-in user before showing anything.
- **Prepared statements throughout** — login, signup, vehicle
  creation, booking, and every AJAX lookup use parameterized queries.
- **My Bookings** — a booking history page, plus a small dashboard
  summary (vehicle count, upcoming services, next service date) on
  the vehicles page.

## Notes on demo/estimated data

- The "Nearest Service Center" panel on the booking page shows one
  fixed, featured demo service center (Alcon Hyundai - Margao) — it
  is not live GPS data.
- Vehicle Health / Service Status figures on the vehicle details page
  are calculated from the KMs and dates you enter (not real sensor
  data), and are clearly framed as estimates/insights.
- The "Redirecting to Payment Gateway" step is a UI simulation only —
  no real payment processor is integrated.
