# Architecture Rules Attributes

The role attributes (`#[Service]`, `#[ValueObject]`, ...) that projects put on their classes, plus `#[Exported]`,
which marks the classes other domains may use. Installed as a production dependency (PHP 8.3–8.5). The rules that
check them live in the sibling package `phpstan-architecture-rules`, installed with `--dev`.

- Attribute classes only, in `src/`, namespace `DaveLiddament\Architecture\Attribute`. No behaviour, no tests.
  An attribute may take constructor arguments (e.g. `#[Exported(to: [...])]`), as `readonly` promoted properties.
- No `require` dependencies other than PHP. Keep it that way: every project that uses the attributes pulls them into
  production.
- Framework-agnostic: never mention a specific framework in code or docs.
- Each attribute is `final`, targets classes only, and has a one-line docblock saying what it means.
- The README is the main deliverable. Keep it concise and starting with the problem the package solves. When adding
  or changing a role, update its README section.
- `composer ci` runs composer validate, PHP CS Fixer (dry run) and PHPStan at max level with strict rules.

## Docker

This project uses Docker for all PHP operations. There is one service per PHP version
(`php83`, `php84`, `php85`). Always use `docker compose run --rm <service>` to run PHP commands.

Examples:
- `docker compose run --rm php85 composer install`
- `docker compose run --rm php85 composer ci`

`vendor/` is shared between services: run `composer update` when switching PHP version.
To rebuild after Dockerfile changes: `docker compose build`
