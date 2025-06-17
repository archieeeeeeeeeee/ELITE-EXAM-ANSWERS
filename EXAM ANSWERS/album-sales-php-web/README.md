# Album Sales PHP Web Application

## Description
This is a PHP-based web application for managing album sales. It provides functionalities for user authentication, viewing and managing albums and artists, and a dashboard for administrative tasks.

## Features
- User login and logout
- Dashboard for managing albums and artists
- RESTful API endpoints for albums, artists, and authentication
- MySQL database integration for storing album and artist data
- Secure authentication checks for protected pages

## Technologies Used
- PHP
- MySQL
- HTML/CSS
- JavaScript (for frontend interactions)
- REST API design

## Setup Instructions
1. Clone the repository.
2. Configure your MySQL database with the following credentials (or update `config/database.php` accordingly):
   - Server: 127.0.0.1
   - Username: root
   - Password: (empty)
   - Database Name: php_album_sales
3. Import the provided database schema and data if available.
4. Place the project in your web server's root directory (e.g., `htdocs` for XAMPP).
5. Access the application via your browser at `http://localhost/album-sales-php-web/`.
6. Use the login page to authenticate and access the dashboard.

## Directory Structure
- `api/` - API endpoints for albums, artists, and authentication
- `config/` - Database configuration files
- `data/` - Data files such as CSVs for album sales reference
- `includes/` - Authentication checks and reusable PHP includes
- `src/` - Static assets like images
- `templates/` - Header and footer templates for consistent UI
- Root PHP files like `index.php`, `dashboard.php`, `login.php`, `logout.php`, and `setup.php`

## Screenshots
Screenshots of the login page and admin dashboard are available in the `EXAM ANSWERS/album-sales-screenshots/` directory.

## Author
Developed by Archie
