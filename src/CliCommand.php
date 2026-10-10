<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * A command line entry point. It reads input, hands the work to services and entities, and reports the result. Keep
 * business logic out of it, so the same work can also be triggered from a controller or a queue.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class CliCommand
{
}
