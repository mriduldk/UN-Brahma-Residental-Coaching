# ITI Online Registration

## Getting Started

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM

### Installation

The project is already set up. If you need to reinstall dependencies:

```bash
composer install
npm install
```

### Database

The database is SQLite. To reset and seed the database:

```bash
php artisan migrate:fresh --seed
```

### Running the App

1. Start the Laravel server:
   ```bash
   php artisan serve
   ```

2. Start the Vite development server:
   ```bash
   npm run dev
   ```

### Default Credentials

Password for all accounts is: `password`

| Role | Email |
|------|-------|
| Admin | `admin@example.com` |
| Administration | `administration@example.com` |
| Student | `student@example.com` |
