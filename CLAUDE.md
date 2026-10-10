# Architecture Rules Attributes

The role attributes (`#[Service]`, `#[ValueObject]`, ...) that projects put on their classes, plus `#[Exported]`,
which marks the classes other domains may use. Installed as a production dependency (PHP 8.3–8.5). The rules that
check them live in the sibling package `phpstan-architecture-rules`, installed with `--dev`.

- Attribute classes only, in `src/`, namespace `DaveLiddament\Architecture\Attribute`. No behaviour and no tests in
  `src/`. Dev tooling in `utils/` (e.g. custom PHPStan rules) may have both.
- An attribute may take constructor arguments (e.g. `#[Exported(to: [...])]`), as `readonly` promoted properties.
- No `require` dependencies other than PHP. Keep it that way: every project that uses the attributes pulls them into
  production.
- Framework-agnostic: nothing may depend on or assume a framework. Naming frameworks to say the package works with
  them is fine. Describing a role in one framework's terms (e.g. "form types") is not.
- Each attribute is `final`, targets classes only, has a docblock fully explaining what it means, and is
  documented in the README. `AttributeConventionsRule` in `utils/phpstan` checks this.
- The README is the main deliverable. Keep it concise and starting with the problem the package solves. When adding
  or changing a role, update its README section.
- The phpstan-architecture-rules README is the source of truth for what the checks do. When this README describes a
  check (e.g. the default layout, or what `#[Exported]` allows), the two must agree: update the other repo too, or
  tell the user what needs changing there.
- `composer ci` runs composer validate, PHP CS Fixer (dry run), PHPStan at max level with strict rules, and PHPUnit
  (the tests for the custom PHPStan rules).

## Docker

This project uses Docker for all PHP operations. There is one service per PHP version
(`php83`, `php84`, `php85`). Always use `docker compose run --rm <service>` to run PHP commands.

Examples:
- `docker compose run --rm php85 composer install`
- `docker compose run --rm php85 composer ci`

`vendor/` is shared between services: run `composer update` when switching PHP version.
To rebuild after Dockerfile changes: `docker compose build`
