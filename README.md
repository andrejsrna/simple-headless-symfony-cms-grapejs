# Simple Headless Symfony CMS with GrapeJS

A modern, lightweight CMS built with Symfony that can operate in both traditional and headless modes. Features a powerful admin interface and GrapeJS for visual content editing.

## Features

### Content Management
- **Articles Management**
  - Create, edit, and delete articles
  - Rich text editing with GrapeJS
  - Article scheduling (publish/unpublish dates)
  - Article categories and tags
  - Featured images with automatic WebP conversion
  - SEO metadata management

- **Pages Management**
  - Static page creation and management
  - Visual page builder with GrapeJS
  - Custom page templates
  - SEO-friendly URLs

- **Media Management**
  - Image upload and management
  - Automatic WebP conversion
  - Image optimization
  - Alt text management
  - Bulk upload support

### Admin Interface
- **Modern Dashboard**
  - Overview of content statistics
  - Recent activity tracking
  - Quick access to common actions
  - Responsive design

- **User Management**
  - Role-based access control (Admin/Editor roles)
  - User creation and management
  - Secure password management
  - User activity logging

### System Features
- **Headless Mode**
  - Toggle between traditional and headless CMS
  - API endpoints for content delivery
  - Configurable API access
  - CORS configuration

- **Security**
  - Role-based access control
  - CSRF protection
  - Secure password hashing
  - Protected admin routes

- **Performance**
  - Image optimization
  - WebP conversion
  - Caching support
  - Optimized database queries

### Technical Features
- Built with Symfony 6.x
- PHP 8.1+ support
- Doctrine ORM
- Twig templating
- GrapeJS integration
- RESTful API
- Modern UI with Tailwind CSS

## Requirements
- PHP 8.1 or higher
- Composer
- MySQL/MariaDB
- Node.js and npm (for asset management)
- GD or Imagick PHP extension

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/simple-headless-symfony-cms-grapejs.git
```

2. Install PHP dependencies:
```bash
composer install
```

3. Configure your database in `.env`:
```
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
```

4. Create database and run migrations:
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. Create an admin user:
```bash
php bin/console app:create-admin
```

6. Start the Symfony development server:
```bash
symfony server:start
```

## Usage

1. Access the admin panel at `/admin`
2. Log in with your admin credentials
3. Start managing your content through the intuitive admin interface

## API Documentation

When in headless mode, the CMS provides RESTful API endpoints:

- `GET /api/articles` - List all articles
- `GET /api/articles/{id}` - Get specific article
- `GET /api/pages` - List all pages
- `GET /api/pages/{id}` - Get specific page

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License - see the LICENSE file for details. 