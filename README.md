The package is intended for avoiding problems with .env and .env.example files content mismatch.

# Installation

Start by requiring the package with composer:

```
composer require justcoded/dotenv-sync
```

or add it to your `composer.json` and run `composer update`

# Usage

Add the next line to your **git pre-commit hooks** (`.git/hooks/pre-commit`) to prevent committing 
if `.env` has params missed in `.env.example` and vice versa. 
Ensure the file `vendor/bin/dotenv-diff` is executable.

```
exec vendor/bin/dotenv-diff {envFileName} {envExampleFileName}
```

To make bin scripts executable just run:
```bash
chmod +x vendor/bin/dotenv-diff vendor/bin/dotenv-sync
```

To **sync missing variables** to your `.env` and `.env.example` files run the next command
```
vendor/bin/dotenv-sync {envFileName} {envExampleFileName}
```

Also you can notify about diff on `composer install`. To do this add such script to your 
`composer.json`:
```js
{
  // ...
  "scripts": {
    "post-install-cmd": [
      "dotenv-diff"
    ],
    "post-update-cmd": [
      "dotenv-diff"
    ]
  }
```
