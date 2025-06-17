# ELITE EXAM ANSWERS

This repository contains all my answers for the ELITE EXAM.

All files in this repository are answers for the exam, including the document `ELITE EXAM 1.2.docx` located in the `FOLDER/` directory.

## Album Sales PHP Web Application

This project includes a PHP-based web application for managing album sales with the following features:

### Models

#### Artists
- Fields:
  - Code
  - Name
- Functions:
  - Read the full details of a created artist
  - Update the full details of a created artist
  - Delete the details of a created artist

#### Albums
- Fields:
  - Year
  - Name
  - Sales
- Functions:
  - Read the full details of an album
  - Update an album
  - Delete an album
  - Add a picture of the album cover

### Migration and Database Relation
- Use the provided CSV file to populate the artists and albums.
- Use Faker to generate other details not present in the CSV file.

### Login
- Admin user can perform logout functionality.
- Credentials require username and password.

### Dashboard
- Display total number of albums sold per artist.
- Display combined album sales per artist.
- Display the top artist who sold the most combined album sales.
- Display a list of albums based on the searched artist.

### Bonus (Laravel API)
- Translate each SQL script to Laravel Eloquent format.
- Provide routes for each script for each scenario.
- API output must be in JSON format.
- Provide an authentication function that grants a bearer token to access the routes.
- Create API routes under `routes.php` in Laravel using the created controller for CRUD operations on:
  - Artist
  - Album
- Endpoints support GET, POST, PUT/PATCH, DELETE.

## How to Run Album-Sales-PHP-Web

1. Clone this repository.
2. Configure your MySQL database with the following credentials (or update `config/database.php` accordingly):
   - Server: 127.0.0.1
   - Username: root
   - Password: (empty)
   - Database Name: php_album_sales
3. Import the provided database schema and data if available.
4. Place the project in your web server's root directory (e.g., `htdocs` for XAMPP).
5. Access the application via your browser at `http://localhost/album-sales-php-web/`.
6. Use the login page to authenticate and access the dashboard.

## Admin Login Credentials

- Username: admin
- Password: password

## Repository Link

The website and all exam answers are uploaded to the repository:

https://github.com/archieeeeeeeeeee/ELITE-EXAM-ANSWERS

## Album Sales Laravel App Bonus

This folder `EXAM ANSWERS/album-sales-laravel-app-bonus/` contains the Laravel implementation of the bonus features for the Album Sales project, including:

- Laravel migrations for artists and albums tables.
- Eloquent models for Artist and Album with relationships.
- Controllers for Artist, Album, Authentication, and Dashboard with CRUD and API endpoints.
- API routes with authentication using bearer tokens.
- Seeder to populate artists and albums from CSV file using Faker for missing details.
- Dashboard API endpoints for album sales statistics.

This Laravel app complements the PHP web application by providing a modern API backend with authentication and advanced features.
