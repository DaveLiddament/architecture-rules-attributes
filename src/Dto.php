<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * A plain data carrier between layers, e.g. the validated contents of a form or an API request.
 *
 * It is deliberately not a value object. A DTO may carry things a value never would, such as entities, and it can be
 * filled in step by step.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Dto
{
}
