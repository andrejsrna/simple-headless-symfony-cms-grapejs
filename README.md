# Symfony CMS Blog

A lightweight content management system built with Symfony 6, featuring a modern admin interface and responsive design.

## Features

### Content Management
- Article creation and editing with rich text editor (Suneditor)
- Image upload with drag-and-drop support
- Article preview before publishing
- Responsive image handling
- Article listing with pagination

### Authentication & Security
- Secure admin area
- User authentication system
- Role-based access control
- CSRF protection
- Secure password hashing

### User Interface
- Modern, responsive design
- Bootstrap 5 for admin interface
- Tailwind CSS for public pages
- Mobile-friendly layout
- Interactive image upload with preview
- Rich text editor with full formatting capabilities

## API Endpoints

### Public Routes
- `GET /` - Homepage with latest articles
- `GET /articles/{id}` - View single article

### Admin Routes (requires authentication)
- `GET /admin/articles` - List all articles
- `GET /admin/articles/new` - Create new article form
- `POST /admin/articles/new` - Submit new article
- `GET /admin/articles/{id}` - View article in admin
- `GET /admin/articles/{id}/edit` - Edit article form
- `POST /admin/articles/{id}/edit` - Update article
- `POST /admin/articles/{id}/delete` - Delete article

### Authentication Routes
- `GET /login` - Login page
- `POST /login` - Login submission
- `GET /logout` - Logout action

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd local-php-blog
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Configure your database in `.env`:
```
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=15&charset=utf8"
```

5. Create the database and run migrations:
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

6. Create an admin user:
```bash
php bin/console app:create-admin admin@example.com yourpassword
```

7. Build assets:
```bash
npm run build
```

8. Start the development server:
```bash
symfony serve
```

## Requirements

- PHP 8.1 or higher
- Composer
- Node.js & NPM
- PostgreSQL
- Symfony CLI (for development)

## Directory Structure

- `src/Controller/` - Application controllers
- `src/Entity/` - Doctrine entities
- `src/Form/` - Form types
- `templates/` - Twig templates
- `assets/` - JavaScript and CSS files
- `public/` - Public files and compiled assets
- `config/` - Application configuration

## Development

To start developing:

1. Start the Symfony development server:
```bash
symfony serve -d
```

2. Watch for asset changes:
```bash
npm run watch
```

## Testing

Run the test suite:
```bash
php bin/phpunit
```

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details. 