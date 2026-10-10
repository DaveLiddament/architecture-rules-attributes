# Architecture Rules Attributes

[![PHP versions: 8.3 to 8.5](https://img.shields.io/badge/php-8.3|8.4|8.5-blue.svg)](https://packagist.org/packages/dave-liddament/architecture-rules-attributes)
[![PHPStan max level](https://img.shields.io/badge/PHPStan-max%20level-brightgreen.svg)](phpstan.neon)
[![License](https://poser.pugx.org/dave-liddament/architecture-rules-attributes/license)](https://github.com/DaveLiddament/architecture-rules-attributes/blob/main/LICENSE.md)

## The problem

A codebase's architecture rules usually live in names, directories, a wiki page or someone's head. Nothing checks
them, so they erode one shortcut at a time. This package lets the code state two kinds of rule, so a tool can check
them, whether the code is written by a person or by an AI agent:

1. **Every class says what type of class it is, and each type has rules.** A class is a value object, a repository,
   a service, and so on. Each of these roles has defined behaviour: value objects are immutable, services are
   stateless, repositories only load and store entities. Once a class declares its role, a "value object" growing a
   setter is an error, not something to spot in review. See [The roles](#the-roles).

   ```php
   use DaveLiddament\Architecture\Attribute\ValueObject;

   #[ValueObject]
   final readonly class Money
   {
   }
   ```

2. **Code is kept together in domains, and each class says whether other domains may use it.** A domain's internals
   stay private to it. The classes other domains may use, such as an entity or a service, are marked `#[Exported]`.
   See [Exporting to other domains](#exporting-to-other-domains).

   ```php
   use DaveLiddament\Architecture\Attribute\Entity;
   use DaveLiddament\Architecture\Attribute\Exported;

   #[Entity]
   #[Exported]
   final class Walk
   {
   }
   ```

This package is just the attributes. A static analysis tool checks the rules: see
[Checking the rules](#checking-the-rules). The attributes have no dependencies and work with any framework, or none.

## Why it helps

Checked rules constrain and guide the code that gets written. A coding agent that gives a value object a setter is
told straight away, and fixes it before anyone looks. The same goes for a person.

They also show reviewers where their time is best spent. A value object that passes all its checks has little left
to get wrong, so it rarely needs a close look. The interesting decisions are in the services, so that's where review
effort goes. This matters more as agents write more of the code, and there's more to review than there is time for.

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

<!-- definition: CliCommand -->

A command line entry point. It reads input, hands the work to services and entities, and reports the result. Keep
business logic out of it, so the same work can also be triggered from a controller or a queue.

<!-- /definition -->

### ConfigProvider

<!-- definition: ConfigProvider -->

The one place primitive configuration lives: API keys, URLs, limits, feature flags. Its properties are scalars, or
lists of scalars.

Services don't take strings and integers in their constructors. They take a config provider. That keeps every
setting in one place you can find, and stops primitives leaking through the app.

<!-- /definition -->

### Controller

<!-- definition: Controller -->

An HTTP entry point. Like a CLI command, it translates a request into calls to services and entities, and turns the
result into a response. Its public methods return one of the allowed response types.

<!-- /definition -->

### Dto

<!-- definition: Dto -->

A plain data carrier between layers, e.g. the validated contents of a form or an API request.

It is deliberately not a value object. A DTO may carry things a value never would, such as entities, and it can be
filled in step by step.

<!-- /definition -->

### Entity

<!-- definition: Entity -->

An object with identity. Two entities are the same if their identity matches, even when their content differs: a
customer who changes their name is still the same customer. Entities are loaded and stored through a
[`#[Repository]`](#repository).

An entity isn't just data. Behaviour that belongs to it lives on it: `$order->cancel()`, rather than an
`OrderCanceller` service that changes the order's status.

<!-- /definition -->

If your persistence library has its own entity attribute, the checks can be configured to treat that as
`#[Entity]` too, so you don't need both.

### QueueGateway

<!-- definition: QueueGateway -->

Sends messages to a queue. It has no rules of its own. It marks a collaborator that a [`#[Service]`](#service) may
depend on, so that sending a message doesn't need a service to hold the queue client directly.

<!-- /definition -->

### QueueProcessor

<!-- definition: QueueProcessor -->

Handles messages taken from a queue. The queue's equivalent of a controller: it unpacks the message and hands the
work to services.

<!-- /definition -->

### Repository

<!-- definition: Repository -->

Loads and stores entities, and hides how they are stored. That could be a database, files, an API, or data generated
at build time. The rest of the app never needs to know.

When one entity owns others, such as an order and its lines, the repository loads and stores the owner, and the owned
entities come with it. There's no separate repository for order lines. In DDD terms, a repository works with aggregate
roots.

<!-- /definition -->

Public methods use a small, fixed vocabulary, so a caller can tell what a method does from its name:

| Prefix | Meaning |
|---|---|
| `find*` | Look up a single entity, which may not exist |
| `get*` | Query: collections, counts, ... |
| `persist*` / `update*` / `delete*` | Writes |
| `has*` / `is*` | Boolean queries |

### Serializer

<!-- definition: Serializer -->

Converts to and from wire formats such as JSON, CSV or an API's payloads. It's the one place array shapes
(`array{...}`) belong: everywhere else, structured data travels as a value object, DTO or view model.

<!-- /definition -->

### Service

<!-- definition: Service -->

A stateless collaborator. It does the work that doesn't belong to a single entity or value object: running a use
case, coordinating several objects, or talking to the outside world.

Every property is another collaborator: a service, repository, queue gateway, serializer, config provider, or an
interface. No primitives and no state, so a service gives the same answer however many times it's called.

In DDD terms, this covers domain, application and infrastructure services.

<!-- /definition -->

### ValueObject

<!-- definition: ValueObject -->

A value with no identity: two value objects with the same content are equal. Money, an email address, a date range.

Every property is itself a value: a primitive, `\DateTimeImmutable`, an enum, another value object, or a list of
these. Value objects are immutable; to "change" one, create a new one. Behaviour lives here too:
`$price->add($tax)`.

<!-- /definition -->

### ViewModel

<!-- definition: ViewModel -->

A snapshot handed to a template. Like a value object, but it holds formatted, ready-to-display values rather than
domain values, so the template has no decisions left to make.

<!-- /definition -->

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

## Exporting to other domains

<!-- definition: Exported -->

Most applications are split into domains, such as `Walks`, `Billing` and `Stats`. A domain should keep its internals
private, so that other domains can't couple themselves to details that should be free to change. But some classes are
meant to be used by other domains: an entity, a value object, a service.

`#[Exported]` marks those classes. A class in a domain can only be used by other domains if it is `#[Exported]`,
wherever it lives in that domain. With `to`, only the domains listed may use it. It isn't a role, so it goes
alongside one.

<!-- /definition -->

```php
use DaveLiddament\Architecture\Attribute\Entity;
use DaveLiddament\Architecture\Attribute\Exported;
use DaveLiddament\Architecture\Attribute\Service;

#[Entity]
#[Exported]                             // any domain may use it
final class Walk {}

#[Service]
#[Exported(to: ['Stats', 'Billing'])]   // only Stats and Billing may use it
final readonly class WalkPlanner {}
```

### How the app is organised by default

To know where one domain ends and the next begins, the checks need a layout. By default,
[phpstan-architecture-rules](#phpstan) expects this:

| Area | Default namespace | What lives there |
|---|---|---|
| **Domain** | `App\<Name>\`, e.g. `App\Walks\` | Every namespace directly under `App\` that isn't one of the areas below |
| **Shared** | `App\Shared\` | Application code used throughout the app, e.g. a `User` entity |
| **Pending** | `App\Pending\` | New code whose domain isn't clear yet. It acts as a domain called `Pending` until it moves to its real home |
| **Lib** | `Lib\` | Code you could take to another project |

Dependencies flow one way: domains → Shared → Lib. Pending is treated like any other domain: other domains may use
its classes only if they are exported.

Within a domain, some roles have their own directory: `Controller\`, `CliCommand\` and `Entity\`. Repositories go in
`Repository\` or the domain's root. Everything else can go wherever suits the domain.

A domain is named after its namespace under `App\`: `Walks` for `App\Walks`. That's the name `to` uses.

These are only defaults. The app namespace and the namespaces of Shared, Pending and Lib can be changed for each
project, and any rule can be turned off. See the
[phpstan-architecture-rules configuration](https://github.com/DaveLiddament/phpstan-architecture-rules#configuration).

### What it means

- **No `#[Exported]`**: the class is internal to its domain. Only its own domain may use it.
- **`#[Exported]`**: every domain may use the class.
- **`#[Exported(to: [...])]`**: only the domains listed may use it. Each one must be an existing domain, so a typo, or
  a domain that has since been renamed, is reported.
- **An exported [`#[Repository]`](#repository) is read-only to other domains.** They may call its `find*`, `get*`,
  `has*` and `is*` methods. Writes go through a service the owning domain exports, so the owning domain stays in
  charge of its data.

Which classes other domains may use is decided by the attribute, not by where the class lives. Making a class
available to another domain doesn't mean moving it and updating every import, and an entity can be exported even
though it must live in `Entity\`.

### What to export

| Can be exported | Should never be exported |
|---|---|
| `#[Service]`, `#[QueueGateway]`, `#[ValueObject]`, `#[Dto]`, `#[Entity]`, `#[Repository]`, interfaces, enums, exceptions | `#[CliCommand]`, `#[Controller]`, `#[QueueProcessor]`, `#[ViewModel]`, `#[Serializer]`, `#[ConfigProvider]`, traits, framework glue (form definitions, event listeners, ...) |

Entry points are invoked by the framework, so nothing should depend on them. View models, serializers and config
providers are details of one domain. Exporting one of these is reported.

### Exported or Shared?

Export a class when it belongs to one domain and a few others need it. If it's used throughout the app, or doesn't
really belong to any one domain, move it to Shared instead. A `to` list that keeps growing is a sign the class belongs
in Shared.

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

The namespace `DaveLiddament\Architecture\Attribute` won't change. Adding an attribute is not a breaking change.
Renaming or removing one is, and only happens in a new major version.

## Contributing

See [Contributing](CONTRIBUTING.md).
