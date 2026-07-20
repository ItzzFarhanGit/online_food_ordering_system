# Online Food Ordering System (PHP + MySQL)

A simple food ordering website with signup/login, menu browsing, order placement,
customer reviews, and a contact form.

## Setup Instructions (XAMPP / WAMP / LAMP)

1. **Copy the project folder**
   Copy the entire `food_ordering` folder into your server's web root:
   - XAMPP (Windows): `C:\xampp\htdocs\food_ordering`
   - WAMP (Windows): `C:\wamp64\www\food_ordering`
   - Linux/Mac (LAMP/MAMP): `/var/www/html/food_ordering`

2. **Start Apache and MySQL**
   Open your XAMPP/WAMP control panel and start both **Apache** and **MySQL**.

3. **Create the database**
   - Open `http://localhost/phpmyadmin`
   - Click **Import** (top menu)
   - Choose the file `database.sql` (included in this project)
   - Click **Go**

   This will automatically:
   - Create the `food_ordering` database
   - Create all 4 required tables: `users`, `orders`, `reviews`, `contact_messages`
   - Insert 2 sample reviews so the Review page isn't empty on first run

   *(Alternative — command line):*
   ```bash
   mysql -u root -p < database.sql
   ```

4. **Check database credentials**
   Open `db.php` and confirm these match your MySQL setup (defaults work for
   most fresh XAMPP/WAMP installs with no root password):
   ```php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "food_ordering";
   ```

5. **Open the site in your browser**
   ```
   http://localhost/food_ordering/home.php
   ```

## Pages

| File | Purpose |
|---|---|
| `home.php` | Landing page with featured dishes |
| `menu.php` | Full menu listing |
| `orders.php` | Order form per dish → goes to `order_success.php` |
| `order_success.php` | Saves the order to the database and shows a confirmation |
| `signup.php` / `login.php` / `logout.php` | User authentication |
| `forgot.php` / `reset_password.php` | Password recovery flow |
| `review.php` | Customers can post and view star ratings/reviews |
| `contact.php` | Contact form, saved to `contact_messages` table |
| `about.php` | About us / team page |

## Notes on fixes applied

This copy of the project has been corrected from the original upload:

- Added the missing `database.sql` (no schema existed before — all 4 tables
  referenced in the PHP code are now defined with proper types and an email
  `UNIQUE` constraint).
- Fixed a broken image path in `home.php` (trailing space in the filename).
- Fixed an unbalanced `</div>` in `menu.php`.
- Removed an unnecessary "Name" field from the login form and cleaned up the
  password-reset success message logic in `login.php`.
- Made `forgot.php` actually validate that the email exists before sending
  the user to `reset_password.php` (previously this check was dead code).
- Fixed `reset_password.php` so the "password updated" message correctly
  shows on the login page after a reset.
- Fixed a SQL-injection risk in `signup.php` (fullname/email weren't being
  escaped) and added a friendly "email already exists" message.
- Removed dead/duplicate order-insert code in `orders.php` and fixed a broken
  navigation link (`order.php` → `menu.php`).
- Hardened `order_success.php` against missing form fields.
- Clamped the star rating in `review.php` to a safe 1–5 range.
- Fixed two invalid CSS rules in `review.css` (`1000 px` → `1000px`,
  `1opx` → `10px`) and an invalid escaped space in a `contact.php` background
  URL.

## Security note

This is a learning/demo project. For real production use you'd also want:
CSRF tokens on all forms, rate-limiting on login/signup, prepared statements
(mysqli/PDO) instead of escaped string interpolation, and server-side
session hardening.
