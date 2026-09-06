# Try it online

https://card.it-wise.pl

## Architectural decisions

I added two roles, `user` and `admin`, so registering an account doesn't automatically give access to gift cards. New users see a dashboard without card data. Permissions are checked on the backend in `CardPolicy`. A simple enum was enough for this task.

I masked card numbers on the list to limit what someone looking at the screen can read. Only the last four digits are visible. An admin can reveal or hide the full number with one click; revealing it sends a request to the backend, which checks permissions first.

I kept validation in Form Requests, permissions in the policy, and list data formatting in `CardResource`. This keeps controllers short and gives each part a clear responsibility, following the single responsibility principle from SOLID. The create and edit screens also share form fields and validation rules to make changes easier.

## Local setup

Requirements: PHP 8.4.1 or newer, Composer, Node.js 22, npm, and a configured database.

```bash
composer setup
php artisan db:seed
composer run dev
```

## Demo accounts

The database seeder creates two local demonstration accounts:

| Role          | Email               | Password   | Card access |
| ------------- | ------------------- | ---------- | ----------- |
| Standard user | `user@example.com`  | `password` | No          |
| Administrator | `admin@example.com` | `password` | Yes         |

These credentials are intended for local development only. Sign in with the administrator account to access the paginated card list and create, edit, or delete cards from the dashboard.

## Quality checks

```bash
composer run ci:check
```
