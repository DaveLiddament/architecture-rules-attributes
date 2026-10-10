<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * Sends messages to a queue. It has no rules of its own. It marks a collaborator that a `#[Service]` may depend on, so
 * that sending a message doesn't need a service to hold the queue client directly.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class QueueGateway
{
}
