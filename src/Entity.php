<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * An object with identity: two entities are the same if their identity matches, even when their content differs.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Entity
{
}
