<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * A stateless collaborator. It does the work that doesn't belong to a single entity or value object: running a use
 * case, coordinating several objects, or talking to the outside world.
 *
 * Every property is another collaborator: a service, repository, queue gateway, serializer, config provider, or an
 * interface. No primitives and no state, so a service gives the same answer however many times it's called.
 *
 * In DDD terms, this covers domain, application and infrastructure services.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Service
{
}
