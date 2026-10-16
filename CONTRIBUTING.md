# Contributing to Dispatch Blog

## How to run the tests

This project does not have an automated test suite yet: there is no PHPUnit
setup and no `composer test` script. Until there is one, run the checks below
before opening a pull request.

Install the dependencies first (PHP 8.2 or later):

```bash
composer install
```

Check the PHP files for syntax errors and lint the templates, configuration
and service container:

```bash
find src -name '*.php' -print0 | xargs -0 -n1 php -l
php bin/console lint:twig templates
php bin/console lint:yaml config
php bin/console lint:container
```

Then smoke-test the site. Start the local server:

```bash
php -S 127.0.0.1:8000 -t public public/router.php
```

In another terminal, check that the index and an article page both return
`200`:

```bash
curl -sf -o /dev/null -w '%{http_code}\n' http://127.0.0.1:8000/
curl -sf -o /dev/null -w '%{http_code}\n' http://127.0.0.1:8000/articles/context-engineering
```

`composer.json` already maps the `App\Tests\` namespace to `tests/`. Automated
tests added later belong there, and this section should then be updated with
the command that runs them.
