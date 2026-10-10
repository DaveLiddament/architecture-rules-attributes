<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * An HTTP entry point. Like a CLI command, it translates a request into calls to services and entities, and turns the
 * result into a response. Its public methods return one of the allowed response types.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Controller
{
}
