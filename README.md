The package is intended for avoiding problems with .env and .env.example files content mismatch.

# Installing via composer

Start by requiring the package with composer:

```
composer require justcoded/sync-env
```

or add it to your `composer.json` and run `composer update`

# Usage

Add the next lines to your pre-commit git hooks (`.git/hooks/pre-commit`) to prevent committing 
if `.env` has params missed in `.env.example` and vice versa

```
exec vendor/bin/diff {envFileName} {envExampleFileName}
```

To add missed variables to your `.env` and `.env.example` files run the next command
```
exec vendor/bin/sync {envFileName} {envExampleFileName}
```