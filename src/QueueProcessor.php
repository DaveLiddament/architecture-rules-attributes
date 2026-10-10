<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * Handles messages taken from a queue. Must be final readonly.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class QueueProcessor
{
}
