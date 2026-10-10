<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * A value with no identity: equality is by content. Must be final readonly.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class ValueObject
{
}
