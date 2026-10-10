<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * Handles messages taken from a queue. The queue's equivalent of a controller: it unpacks the message and hands the
 * work to services.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class QueueProcessor
{
}
