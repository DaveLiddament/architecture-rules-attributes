<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * A value with no identity: two value objects with the same content are equal. Money, an email address, a date range.
 *
 * Every property is itself a value: a primitive, `\DateTimeImmutable`, an enum, another value object, or a list of
 * these. Value objects are immutable; to "change" one, create a new one. Behaviour lives here too: `$price->add($tax)`.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class ValueObject
{
}
