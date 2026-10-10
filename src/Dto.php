<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * A plain data carrier between layers. Must be final. Deliberately not a value object.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Dto
{
}
