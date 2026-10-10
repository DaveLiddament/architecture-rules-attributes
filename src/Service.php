<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * A stateless collaborator. Must be final readonly.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Service
{
}
