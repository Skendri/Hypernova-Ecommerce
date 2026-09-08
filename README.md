# Hypernova Ecommerce

Hypernova Ecommerce is a PHP-powered e-commerce and content platform for showcasing products, publishing blog content, and delivering marketplace experiences with a modern responsive UI. The project combines a product marketplace, seller dashboard, blog publishing workflow, and a news API integration into a single web application suitable for local development with XAMPP.

## Project Overview

Hypernova Ecommerce is designed to support a lightweight online commerce workflow where sellers can manage product listings, publish content, and monitor business activity through a dashboard. The platform includes authentication, product management, blog post creation, image upload handling, and external news content integration.

## Features

- Secure user authentication with login, registration, account creation, and password reset flows
- Seller-friendly dashboard for monitoring products, categories, pricing, and store activity
- Product listing creation and editing with category, price, description, phone number, image upload, and product status fields
- Blog publishing workflow for creating and managing posts with images and content status
- Cart and product browsing experience across the storefront
- News content integration using a public NewsAPI endpoint
- Responsive styling using Bootstrap and custom CSS assets
- PHPMailer-based email support for password recovery and notifications

## Tech Stack

- PHP 8+
- MySQL / MariaDB
- Composer
- PHPMailer
- Dotenv environment configuration
- Bootstrap 5
- JavaScript for client-side UI interactions

## Project Structure

```text
Hypernova-Ecommerce/
├── api/                  # Backend endpoints for products, blogs, dashboard, cart and news
├── assets/               # CSS, JavaScript, image and upload assets
├── auth/                 # Login, signup, logout and password reset flows
├── components/           # Shared UI sections such as navbar and footer
├── config/               # Database and environment configuration
├── database/             # Database import and schema files
├── includes/              # Shared PHP helper logic and validation utilities
├── pages/                # Main application pages
├── vendor/               # Composer dependencies
├── composer.json         # Composer dependency definition
├── index.php             # Application entry point redirect
└── README.md             # Project documentation
```

## Requirements

Before installing and running the application locally, make sure the following tools are available:

- XAMPP or WAMP
- PHP 8+
- Composer
- MySQL database server
- A valid Gmail or SMTP-enabled account for mail delivery
- A NewsAPI key for the news integration

## Local Installation

1. Clone or copy the project into your local web server directory:

   ```text
   C:\xampp\htdocs\Hypernova-Ecommerce
   ```

2. Install PHP dependencies with Composer:

   ```bash
   composer install
   ```

3. Create a local environment file from the sample configuration:

   ```text
   config/.env
   ```

   Use values similar to:

   ```env
   DB_SERVER=localhost
   DB_USER=root
   DB_PASS=
   DB_NAME=hypernova_ecommerce

   SMTP_HOST=smtp.gmail.com
   SMTP_USERNAME=your-email@gmail.com
   SMTP_PASSWORD=your-app-password
   SMTP_PORT=587

   API_KEY=your-news-api-key
   ```

4. Create the database named `hypernova_ecommerce` or the name configured in your environment file.

5. Import the SQL files available in `database/` when needed. The blog posts schema is available in `database/blog_posts.sql`.

6. Start your Apache and MySQL services through XAMPP.

7. Visit the project locally:

   ```text
   http://localhost/Hypernova-Ecommerce/
   ```

## Environment Configuration

The project loads configuration values from PHP environment variables using `vlucas/phpdotenv`. Database access is configured in `config/database.php`, while mail delivery is configured in `mailer.php` through the PHPMailer flow.

For the project to run properly, the following environment variables should be available:

- `DB_SERVER`
- `DB_USER`
- `DB_PASS`
- `DB_NAME`
- `SMTP_HOST`
- `SMTP_USERNAME`
- `SMTP_PASSWORD`
- `SMTP_PORT`
- `API_KEY`

## Application Workflow

1. Browse the storefront from the home page.
2. Register or log in through the authentication pages in `auth/`.
3. Create or update products from the seller/product management flow.
4. Publish blog posts through the blog publishing workflow.
5. Use the dashboard to review product and activity information.
6. Use the cart and product view pages for the shopping experience.

## API Endpoints

The server exposes backend services through the `api/` folder:

- `fetch_products.php` for retrieving product records
- `save_product.php` and `update_product.php` for product lifecycle actions
- `delete_product.php` for product deletion
- `fetch_blog_posts.php` and `save_blog_post.php` for blog workflows
- `dashboard_stats.php` for dashboard analytics
- `newsApi.php` and `api.php` for external news content retrieval

## Notes

- Uploaded files and images are stored under `assets/uploads/`.
- The project is primarily focused on local development and classroom or portfolio demonstration workflows.
- News integration depends on a valid NewsAPI key being configured in the project environment.
- SMTP credentials should be managed carefully and should not be committed into public repositories.

## License

This project is intended for educational and development purposes. Update the license file or add a project-specific license before production use.

## Contributing

Contributions are welcome. If you want to improve the application, please create a feature branch, add or update tests where possible, and submit a pull request with a clear description of the changes.

