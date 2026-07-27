# Hypernova Ecommerce

Hypernova Ecommerce is a PHP-based web application that combines a simple marketplace, seller dashboard, blog publishing workflow, and news integration into one platform. The project is built for local development with XAMPP and uses MySQL for data storage.

## What this project includes

- User authentication with login, registration, and password reset
- A seller-focused dashboard for managing products
- Product publishing with image uploads and status controls
- A blog publishing system for posts, drafts, and cover images
- News integrations from external APIs
- A responsive front end built with Bootstrap and custom CSS

## Tech stack

- PHP
- MySQL / MariaDB
- Bootstrap 5
- PHPMailer
- Composer
- JavaScript for page interactions

## Main project structure

- auth/ – login, registration, password reset, and logout flows
- api/ – backend endpoints for products, blogs, dashboard statistics, and news
- pages/ – main pages such as home, dashboard, pricing, sell product, and product view
- components/ – shared UI components like the navbar and footer
- config/ – database connection and environment configuration
- includes/ – helper functions for API responses, products, and validation
- assets/ – CSS, JavaScript, and uploaded media files
- database/ – SQL schema files
- vendor/ – Composer dependencies

## Features overview

### User accounts

Users can register, log in, and recover passwords through the authentication pages under the auth folder.

### Marketplace

Sellers can publish products with title, category, price, description, phone number, listing status, and multiple images.

### Dashboard

The dashboard gives sellers a quick overview of their listings, totals, pricing insights, and recent activity.

### Blog system

Users can publish blog posts with excerpts, content, status, and cover images.

### News API integration

The app fetches external news content from a public API and displays it on the home page.

## Requirements

Before running the project locally, make sure you have:

- XAMPP or WAMP installed
- PHP 8+ available
- Composer installed
- MySQL running

## Installation and setup

1. Place the project inside your local web server directory, for example:
   - XAMPP: C:\xampp\htdocs\Hypernova-Ecommerce

2. Create your environment file.
   - The application expects environment variables from the config folder.
   - Create a file named config/.env and add your database and mail settings.
   - You can use the example values from .env.example as a starting point.

3. Create the database.
   - Create a database named hypernova_ecommerce (or the name you define in your .env file).

4. Import the SQL schema.
   - The database/blog_posts.sql file contains the blog posts table structure.
   - Product tables are also handled by the app during runtime, but importing the blog schema is recommended.

5. Install Composer dependencies.
   Run this in the project root:

   ```bash
   composer install
   ```

6. Start Apache and MySQL from XAMPP.

7. Open the project in your browser:

   ```text
   http://localhost/Hypernova-Ecommerce/
   ```

## Environment variables

A typical config/.env file should include values similar to the following:

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

## Running the app

- Visit the home page to browse the marketplace and news content.
- Use the login and register pages in the auth folder for account access.
- Use the sell product and pricing pages to publish products and blog posts.

## Notes

- The app uses Bootstrap and custom CSS for styling.
- Uploaded images are stored under the assets/uploads folder.
- If you want the news integration to work properly, a valid API key is required.
- The project is intended for local development and learning purposes.

## Summary

This README was created to help you understand the purpose of the project, how it is organized, and how to set it up locally. It covers the main features, required tools, folder structure, environment setup, and basic usage steps.
