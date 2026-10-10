<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * A command line entry point. Must be final.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class CliCommand
{
}
