# Architecture Rules Attributes

[![PHP versions: 8.3 to 8.5](https://img.shields.io/badge/php-8.3|8.4|8.5-blue.svg)](https://packagist.org/packages/dave-liddament/architecture-rules-attributes)
[![PHPStan max level](https://img.shields.io/badge/PHPStan-max%20level-brightgreen.svg)](phpstan.neon)
[![License](https://poser.pugx.org/dave-liddament/architecture-rules-attributes/license)](https://github.com/DaveLiddament/architecture-rules-attributes/blob/main/LICENSE.md)

## The problem

Every codebase has roles: services, repositories, value objects, controllers. But a class's role usually lives
somewhere you can't check: its name (`InvoiceService`), its directory (`Repository/`), or someone's head.

So nothing stops a "service" quietly picking up state, or a "value object" growing a setter. And a newcomer can't
tell what a class is meant to be without asking.

This package lets each class say what it is:

```php
use DaveLiddament\Architecture\Attribute\Service;

#[Service]
final readonly class InvoiceSender
{
}
```

Once a class declares its role, the rules for that role can be checked: for example, a `#[Service]` that isn't
`final readonly`, or a `#[ValueObject]` holding a mutable date. See [Checking the rules](#checking-the-rules).

This package is just the attributes. It has no dependencies and works with any framework, or none.

## Installation

Install the attributes as a normal dependency, because your production code uses them:

```shell
composer require dave-liddament/architecture-rules-attributes
```

The tools that check the rules are installed separately, as dev dependencies. Keeping them apart means:

- **Production code only depends on what's in `require`.** Dependency checkers such as
  [composer-require-checker](https://github.com/maglnet/ComposerRequireChecker) need no extra configuration.
- **The analysis tooling stays out of production.** The attributes package contains no code that runs.

## Checking the rules

The attributes on their own don't check anything. A static analysis tool does that. Currently only
[PHPStan](https://phpstan.org) is supported.

### PHPStan

Install [phpstan-architecture-rules](https://github.com/DaveLiddament/phpstan-architecture-rules) as a dev
dependency:

```shell
composer require --dev dave-liddament/phpstan-architecture-rules
```

If you use [phpstan/extension-installer](https://github.com/phpstan/extension-installer) you're ready to go.
Otherwise, include the extension in your `phpstan.neon`:

```neon
includes:
    - vendor/dave-liddament/phpstan-architecture-rules/extension.neon
```

Every rule can be configured or turned off. See the
[phpstan-architecture-rules README](https://github.com/DaveLiddament/phpstan-architecture-rules#configuration) for
the rules each role enforces and the configuration options.

## Why attributes?

- **The role is in the code.** It sits on the class, so reviewers see it in the diff and it can't drift away from
  the class like a wiki page can.
- **Easy to find.** "Find usages" on `#[Repository]` lists every repository in the app.
- **No runtime cost.** PHP only loads an attribute's class when something reflects on it and asks for an instance.
  Nothing in this package runs in production.
- **Not tied to naming or directories.** A class is a service because it says so, not because its name ends in
  `Service`.

## The roles

| Attribute | What it is |
|---|---|
| [`#[CliCommand]`](#clicommand) | A command line entry point |
| [`#[ConfigProvider]`](#configprovider) | The one place primitive configuration lives |
| [`#[Controller]`](#controller) | An HTTP entry point |
| [`#[Dto]`](#dto) | A plain data carrier between layers |
| [`#[Entity]`](#entity) | An object with identity |
| [`#[QueueGateway]`](#queuegateway) | Sends messages to a queue |
| [`#[QueueProcessor]`](#queueprocessor) | Handles messages taken from a queue |
| [`#[Repository]`](#repository) | Loads and stores entities |
| [`#[Serializer]`](#serializer) | Converts to and from wire formats |
| [`#[Service]`](#service) | A stateless collaborator |
| [`#[ValueObject]`](#valueobject) | A value with no identity |
| [`#[ViewModel]`](#viewmodel) | A snapshot handed to a template |

All the attributes live in `DaveLiddament\Architecture\Attribute`. This section describes what each role means. For
the exact rules, and how to configure or turn them off, see [Checking the rules](#checking-the-rules).

### CliCommand

A command line entry point. It reads input, hands the work to services and entities, and reports the result. Keep
business logic out of it, so the same work can also be triggered from a controller or a queue.

### ConfigProvider

The one place primitive configuration lives: API keys, URLs, limits, feature flags. Its properties are scalars, or
lists of scalars.

Services don't take strings and integers in their constructors. They take a config provider. That keeps every
setting in one place you can find, and stops primitives leaking through the app.

### Controller

An HTTP entry point. Like a CLI command, it translates a request into calls to services and entities, and turns the
result into a response. Its public methods return one of the allowed response types.

### Dto

A plain data carrier between layers, e.g. the validated contents of a form or an API request.

It is deliberately not a value object. A DTO may carry things a value never would, such as entities, and it can be
filled in step by step.

### Entity

An object with identity. Two entities are the same if their identity matches, even when their content differs: a
customer who changes their name is still the same customer. Entities are loaded and stored through a
[`#[Repository]`](#repository).

An entity isn't just data. Behaviour that belongs to it lives on it: `$order->cancel()`, rather than an
`OrderCanceller` service that changes the order's status.

If your persistence library has its own entity attribute, the checks can be configured to treat that as
`#[Entity]` too, so you don't need both.

### QueueGateway

Sends messages to a queue. It has no rules of its own. It marks a collaborator that a [`#[Service]`](#service) may
depend on, so that sending a message doesn't need a service to hold the queue client directly.

### QueueProcessor

Handles messages taken from a queue. The queue's equivalent of a controller: it unpacks the message and hands the
work to services.

### Repository

Loads and stores entities, and hides how they are stored. That could be a database, files, an API, or data generated
at build time. The rest of the app never needs to know.

When one entity owns others, such as an order and its lines, the repository loads and stores the owner, and the owned
entities come with it. There's no separate repository for order lines. In DDD terms, a repository works with aggregate
roots.

Public methods use a small, fixed vocabulary, so a caller can tell what a method does from its name:

| Prefix | Meaning |
|---|---|
| `find*` | Look up a single entity, which may not exist |
| `get*` | Query: collections, counts, ... |
| `persist*` / `update*` / `delete*` | Writes |
| `has*` / `is*` | Boolean queries |

### Serializer

Converts to and from wire formats such as JSON, CSV or an API's payloads. It's the one place array shapes
(`array{...}`) belong: everywhere else, structured data travels as a value object, DTO or view model.

### Service

A stateless collaborator. It does the work that doesn't belong to a single entity or value object: running a use
case, coordinating several objects, or talking to the outside world.

Every property is another collaborator: a service, repository, queue gateway, serializer, config provider, or an
interface. No primitives and no state, so a service gives the same answer however many times it's called.

In DDD terms, this covers domain, application and infrastructure services.

### ValueObject

A value with no identity: two value objects with the same content are equal. Money, an email address, a date range.

Every property is itself a value: a primitive, `\DateTimeImmutable`, an enum, another value object, or a list of
these. Value objects are immutable; to "change" one, create a new one. Behaviour lives here too:
`$price->add($tax)`.

### ViewModel

A snapshot handed to a template. Like a value object, but it holds formatted, ready-to-display values rather than
domain values, so the template has no decisions left to make.

## Choosing a role

Work down this list and stop at the first match:

1. **Is it an entry point?** HTTP: [`#[Controller]`](#controller). Command line:
   [`#[CliCommand]`](#clicommand). Queue: [`#[QueueProcessor]`](#queueprocessor).
2. **Does it have identity that outlives its content?** [`#[Entity]`](#entity).
3. **Does it load or store entities?** [`#[Repository]`](#repository).
4. **Is it equal to anything with the same content?** [`#[ValueObject]`](#valueobject).
5. **Is it prepared for a template?** [`#[ViewModel]`](#viewmodel).
6. **Does it carry data between layers?** [`#[Dto]`](#dto).
7. **Does it hold configuration?** [`#[ConfigProvider]`](#configprovider).
8. **Does it convert to or from a wire format?** [`#[Serializer]`](#serializer).
9. **Does it send messages to a queue?** [`#[QueueGateway]`](#queuegateway).
10. **Does it do work using other collaborators?** [`#[Service]`](#service).

If a class fits two roles, it's probably doing two jobs. Split it. If it fits none, it's probably holding state it
shouldn't, or it's framework glue that the checks can be told to exempt.

Roles are for classes. Enums, interfaces and traits don't need one.

## Why not DDD?

The roles borrow DDD's names where they mean the same thing (entity, value object, repository), but this isn't a full
DDD implementation. That's deliberate. If you do use DDD, they fit alongside it: a repository works with aggregate
roots, and a service covers domain, application and infrastructure services.

DDD's building blocks (aggregates, domain events, domain services) pay off in complex domains. Most simpler web
applications don't need a full DDD model, and they're who these roles are for. The most important parts of DDD, a
shared language with domain experts and clear boundaries between contexts, can't be checked with an attribute anyway.

It's quite likely this isn't exactly how you'd do it. The good news is that with coding agents, you don't have to take
it or leave it. Point your agent of choice at this package and [the rules that check it](#checking-the-rules), and ask
it to build something that fits exactly how you want to work. A full DDD version, for example.

## Stability

The namespace `DaveLiddament\Architecture\Attribute` won't change. Adding a role is not a breaking change. Renaming or
removing one is, and only happens in a new major version.

## Contributing

See [Contributing](CONTRIBUTING.md).
