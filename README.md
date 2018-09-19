The package is intended for avoiding problems with .env and .env.example files content mismatch.

# Installation

Start by requiring the package with composer:

```
composer require justcoded/sync-env
```

or add it to your `composer.json` and run `composer update`

# Usage

Add the next line to your pre-commit git hooks (`.git/hooks/pre-commit`) to prevent committing 
if `.env` has params missed in `.env.example` and vice versa. Ensure the file `vendor/bin/diff` is executable.

```
exec vendor/bin/diff {envFileName} {envExampleFileName}
```

To add missed variables to your `.env` and `.env.example` files run the next command
```
vendor/bin/sync {envFileName} {envExampleFileName}
```