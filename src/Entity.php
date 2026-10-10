<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * An object with identity. Two entities are the same if their identity matches, even when their content differs: a
 * customer who changes their name is still the same customer. Entities are loaded and stored through a `#[Repository]`.
 *
 * An entity isn't just data. Behaviour that belongs to it lives on it: `$order->cancel()`, rather than an
 * `OrderCanceller` service that changes the order's status.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Entity
{
}
