<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * A snapshot handed to a template. Must be final readonly.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class ViewModel
{
}
