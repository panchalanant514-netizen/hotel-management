# Grand Horizon – Hotel Management System
Full-stack web project: **HTML + CSS + JavaScript** (frontend) and **PHP + MySQL** (backend).

## 1. How to run (XAMPP / WAMP)
1. Install **XAMPP** and start **Apache** and **MySQL**.
2. Copy the whole `hotel-management` folder into `C:\xampp\htdocs\`.
3. Open **http://localhost/phpmyadmin** → *Import* → choose `database/hotel_db.sql` → *Go*.
   (This creates the database `hotel_db`, all tables and demo data.)
4. Open **http://localhost/hotel-management/** in your browser.
5. If your MySQL has a password, change it in `includes/config.php`.

### Demo logins
| Role  | Email            | Password  |
|-------|------------------|-----------|
| Admin | admin@hotel.com  | admin123  |
| Guest | guest@hotel.com  | guest123  |

## 2. Features
**Guest side:** home page with availability search, room listing with filters (type, price, guests, dates),
live price calculator, register / login, book a room, view and cancel own bookings, contact form.

**Admin panel (`/admin`):** dashboard with statistics, manage bookings (confirm / check-in / check-out / cancel),
add / edit / delete rooms with image upload, set room to maintenance, view guests, read contact messages.

## 3. Folder structure
```
hotel-management/
├── index.php            Home page (hero, search, featured rooms)
├── rooms.php            Room list + search filters + availability check
├── booking.php          Booking form (validation + overlap check)
├── my-bookings.php      Guest's bookings, cancel option
├── login.php / register.php / logout.php
├── about.php / contact.php
├── includes/
│   ├── config.php       DB connection (PDO), session, helper functions
│   ├── header.php       Navigation bar (shared by all pages)
│   └── footer.php       Footer + loads main.js
├── admin/               Admin panel (dashboard, bookings, rooms, room-form, users, messages)
├── assets/
│   ├── css/style.css    All styling (responsive)
│   ├── js/main.js       All JavaScript
│   └── images/          Site images + images/rooms/ (room photos, uploads go here)
└── database/hotel_db.sql  Database structure + demo data
```

## 4. Database tables
| Table    | Purpose                                   | Key columns |
|----------|-------------------------------------------|-------------|
| users    | guests and admin                          | name, email, password (hashed), role |
| rooms    | hotel rooms                               | room_number, type, price, capacity, image, status |
| bookings | reservations (links users ↔ rooms)        | user_id, room_id, check_in, check_out, total_price, status |
| messages | contact-form messages                     | name, email, subject, message |

Relationships: `bookings.user_id → users.id` and `bookings.room_id → rooms.id` (foreign keys).

## 5. What to show your teacher (best files to explain)
1. **database/hotel_db.sql** – tables, primary/foreign keys, demo data.
2. **includes/config.php** – PDO database connection, sessions, helper functions.
3. **register.php + login.php** – `password_hash()` / `password_verify()`, sessions, role-based redirect.
4. **booking.php** – server-side validation, availability (date-overlap) query, INSERT.
5. **rooms.php** – dynamic SQL filters with prepared statements.
6. **assets/js/main.js** – mobile menu, date rules, live price calculator, form validation, animations.
7. **assets/css/style.css** – CSS variables, flexbox/grid layout, responsive media queries.
8. **admin/** – CRUD operations (Create, Read, Update, Delete), image upload, status management.

## 6. Security techniques used (good viva points)
- **Prepared statements (PDO)** → prevents SQL injection.
- **password_hash()** → passwords are never stored as plain text.
- **htmlspecialchars() via `e()`** → prevents XSS.
- **CSRF tokens** in every POST form.
- **Session checks** (`requireLogin`, `requireAdmin`) protect pages.
- **File-upload validation** (extension, size, real image check).

## 7. How the booking availability check works
A room is busy if another active booking overlaps the requested dates:
`existing.check_in < new_check_out AND existing.check_out > new_check_in`
(see `isRoomAvailable()` in `includes/config.php`).

## 8. Common viva questions
- *Why PDO?* Safer (prepared statements) and works with many databases.
- *Difference between GET and POST?* GET shows data in URL (search filters); POST hides data (login, booking).
- *What is a session?* Server-side storage that remembers the logged-in user across pages.
- *Frontend vs backend here?* HTML/CSS/JS run in the browser; PHP/MySQL run on the server.
- *How is the total price calculated?* `nights × price_per_night` – JavaScript shows it live, PHP recalculates it on the server for safety.

## 9. Ideas to extend
Online payment gateway, email confirmation, room reviews, invoice PDF, reports with charts.

*Note: room/gallery pictures are generated illustrations. Replace files in `assets/images/rooms/` with real photos of the same name if you wish.*
