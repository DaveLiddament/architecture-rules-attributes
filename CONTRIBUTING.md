# Contributing

Contributions are welcome.

## PHP versions supported

This library supports PHP 8.3, 8.4 and 8.5. It MUST support all PHP versions that are in
[active or security](https://www.php.net/supported-versions.php) support.

## Keep it dependency free

This package is installed as a production dependency, so it has no dependencies of its own. It contains attribute
classes only: no behaviour, no rules. The rules that check the attributes live in
[phpstan-architecture-rules](https://github.com/DaveLiddament/phpstan-architecture-rules).

Adding, renaming or removing an attribute is a breaking change for that package too, so open an issue first.

## Attribute conventions

Every attribute is `final`, targets classes only, has a docblock explaining what it means, only has `readonly`
promoted properties, and is documented in the README. A custom PHPStan rule in [`utils/phpstan`](utils/phpstan)
checks this as part of `composer ci`.

Each attribute's definition is in the README, between `<!-- definition: Name -->` and `<!-- /definition -->`
markers, and its docblock repeats it word for word (links become plain text). A second rule checks they match, so
when you change a definition, change both.

## Docker setup

All development is done through Docker. There is one service per supported PHP version:
`php83`, `php84` and `php85`.

### Install dependencies

`vendor/` is shared between all PHP versions, so install dependencies with the PHP
version you want to work in (e.g. 8.5):

```shell
docker compose run --rm php85 composer install
```

If you switch PHP versions, run `composer update` in the new version first.

### Run composer scripts

```shell
docker compose run --rm php<version> composer <script>
```

For example, to fix code style on PHP 8.5:

```shell
docker compose run --rm php85 composer cs-fix
```

To run all checks on every supported PHP version:

```shell
for v in 83 84 85; do docker compose run --rm php$v sh -c 'composer update -n && composer ci' || break; done
```

See the `scripts` section of [composer.json](composer.json) for all available scripts.

### Shell access

```shell
docker compose run --rm php83 bash
```
