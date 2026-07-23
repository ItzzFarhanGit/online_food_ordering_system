# Online Food Ordering System (PHP + MySQL)

A full food ordering website with signup/login + email OTP verification,
forgot-password with OTP, menu browsing, real payment methods (Cash on
Delivery, Card via Stripe, UPI), customer reviews, a contact form, and a
full admin panel to manage orders, menu items, users, reviews and messages.

## What's new in this version

- ✅ **Email OTP verification** on signup, and OTP-based password reset
- ✅ **Real payment methods**: Cash on Delivery, Card payments (Stripe
  Checkout), and UPI (QR code + deep link)
- ✅ **Admin panel** at `/admin` (username `Farhan`, password `Farhan1234`)
  to manage orders, the menu, users, reviews and contact messages
- ✅ Menu items now live in the database, so the admin panel can add/edit/
  delete dishes without touching any code
- ✅ All settings (database, email, Stripe, UPI, admin login) are in one
  place: `config.php`

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

   This creates the `food_ordering` database and all tables: `users`,
   `otps`, `menu_items`, `orders`, `reviews`, `contact_messages` - plus
   6 starter dishes and 2 sample reviews.

   *(Alternative — command line):*
   ```bash
   mysql -u root -p < database.sql
   ```

4. **Open `config.php` and fill in your real settings**
   This is the only file you need to edit. It's split into clearly
   labelled sections:

   ```php
   // Database (defaults work for most fresh XAMPP/WAMP installs)
   define('DB_HOST', 'localhost');
   define('DB_USERNAME', 'root');
   define('DB_PASSWORD', '');
   define('DB_NAME', 'food_ordering');

   // Your site's base URL (needed for Stripe redirect links)
   define('SITE_URL', 'http://localhost/food_ordering');

   // Admin login - change any time, takes effect immediately
   define('ADMIN_USERNAME', 'Farhan');
   define('ADMIN_PASSWORD', 'Farhan1234');
   ```

5. **Open the site in your browser**
   ```
   http://localhost/food_ordering/home.php
   ```

## Turning on real OTP emails (optional but recommended)

By default `SEND_REAL_EMAILS` is `false`, so the site works immediately:
every OTP is shown on-screen in a yellow "DEV MODE" box so you can test
signup/login/reset without any email setup.

To send real emails:

1. Install PHPMailer via Composer, from the project root:
   ```bash
   composer require phpmailer/phpmailer
   ```
2. In `config.php`, set your SMTP details and flip the switch:
   ```php
   define('SMTP_HOST', 'smtp.gmail.com');
   define('SMTP_PORT', 587);
   define('SMTP_USERNAME', 'your_email@gmail.com');
   define('SMTP_PASSWORD', 'your_app_password_here'); // Gmail "App Password"
   define('SMTP_FROM_EMAIL', 'your_email@gmail.com');
   define('SEND_REAL_EMAILS', true);
   ```
   Gmail App Passwords: https://myaccount.google.com/apppasswords
   (Any other SMTP provider - Outlook, SendGrid, Mailgun, your host's own
   mail server, etc. - also works, just change the host/port/credentials.)

If real sending ever fails (wrong password, no internet, etc.) the site
automatically falls back to showing the OTP on-screen instead of leaving
the customer stuck.

## Turning on real Card payments (Stripe)

1. Create a free Stripe account: https://dashboard.stripe.com/register
2. Grab your **test** keys from https://dashboard.stripe.com/test/apikeys
3. Paste them into `config.php`:
   ```php
   define('STRIPE_SECRET_KEY', 'sk_test_...');
   define('STRIPE_PUBLISHABLE_KEY', 'pk_test_...');
   define('CURRENCY_CODE', 'usd'); // must be a currency your Stripe account supports
   ```
4. Test card payments with Stripe's test card `4242 4242 4242 4242`, any
   future expiry date, any CVC.
5. When you're ready to accept real money, switch to your **live** keys
   (`sk_live_...` / `pk_live_...`) - no other code changes needed.

Until you add real keys, customers will see a friendly "Card payments not
set up yet" message and can still order via Cash on Delivery or UPI.

## Turning on real UPI payments

Just set your UPI ID in `config.php`:
```php
define('UPI_VPA', 'yourbusiness@okhdfcbank');
define('UPI_PAYEE_NAME', 'Your Business Name');
```
Customers scan the generated QR code (or tap the link on mobile) with any
UPI app, pay you directly, then enter their UPI reference number to
confirm the order. Since UPI has no public API for a small business to
auto-verify payment, the order is marked **"Pending Verification"** so you
can confirm it manually in the admin panel once the money lands in your
account.

## Admin Panel

Visit `http://localhost/food_ordering/admin/login.php` (there's also a
small "Admin Login" link in the home page footer).

- **Username:** `Farhan`
- **Password:** `Farhan1234`

From the admin panel you can:
- View live stats and recent orders (**Dashboard**)
- Update order status (Placed → Preparing → Out for Delivery → Delivered)
  and see payment method/status for every order (**Orders**)
- Add, edit or delete dishes shown on the Menu/Order/Home pages (**Menu Items**)
- View registered users, manually mark them verified, or delete accounts (**Users**)
- View and delete customer reviews (**Reviews**)
- View and delete messages submitted through the Contact page (**Contact Messages**)

To change the admin password, just edit `ADMIN_PASSWORD` in `config.php`.

## Pages

| File | Purpose |
|---|---|
| `home.php` | Landing page with featured dishes pulled from the database |
| `menu.php` | Full menu listing (from database) |
| `orders.php` | Order form per dish → `payment.php` |
| `payment.php` | Choose Cash on Delivery, Card (Stripe), or UPI |
| `process_cod.php` / `process_upi.php` | Finalize COD / UPI orders |
| `create_stripe_session.php` | Starts a real Stripe Checkout session |
| `payment_success.php` / `payment_cancel.php` | Stripe redirect handlers |
| `order_success.php` | Saves the completed order and shows a confirmation |
| `signup.php` / `login.php` / `logout.php` | User authentication |
| `verify_otp.php` | Email OTP verification (signup + password reset) |
| `forgot.php` / `reset_password.php` | OTP-protected password recovery flow |
| `review.php` | Customers can post and view star ratings/reviews |
| `contact.php` | Contact form, saved to `contact_messages` table |
| `about.php` | About us / team page |
| `admin/*` | Admin panel (see above) |

## Hosting it for real

Any standard shared/VPS PHP+MySQL host works (the code uses plain
`mysqli` and core PHP - no exotic requirements):

1. Upload the whole `food_ordering` folder via FTP/File Manager, or `git`.
2. Create a MySQL database on your host and import `database.sql`
   through their phpMyAdmin (or `mysql` CLI).
3. Update `config.php` with the host's database credentials (usually
   found in your hosting control panel) and your real `SITE_URL`
   (e.g. `https://www.yoursite.com`).
4. Fill in real SMTP and Stripe details as described above.
5. Make sure your host's PHP has the `curl` and `mysqli` extensions
   enabled (nearly all hosts have these on by default).
6. Visit `https://www.yoursite.com/home.php` to confirm everything works,
   then place a real test order end-to-end before sharing the link.

## Security notes

- Passwords are hashed with `password_hash()` / verified with
  `password_verify()` - never stored in plain text.
- OTP codes are hashed before being stored and expire after 10 minutes.
- All SQL uses escaped strings; consider migrating to prepared
  statements (mysqli/PDO) if you extend this project further.
- The admin account is a single hardcoded login as requested. For a
  multi-admin or higher-security setup, move it into a database table
  with a hashed password instead of a plain-text constant in `config.php`.
- Add CSRF tokens and rate-limiting on login/signup/OTP forms before
  using this in a high-traffic production environment.
- Always use HTTPS in production - required by Stripe and by any
  browser handling passwords/payment info.
