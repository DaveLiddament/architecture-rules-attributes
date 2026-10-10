<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * An HTTP entry point. Public methods must return one of the allowed controller return types.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Controller
{
}
