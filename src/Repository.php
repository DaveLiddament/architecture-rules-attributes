<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * Loads and stores entities, hiding how they are stored. Must be final readonly.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Repository
{
}
