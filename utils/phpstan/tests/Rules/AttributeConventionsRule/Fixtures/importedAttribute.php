<?php

declare(strict_types=1);

namespace Utils\Phpstan\Tests\Rules\AttributeConventionsRule\Fixtures\Attribute;

use Attribute;

/**
 * Declared with an imported Attribute, which is the same thing.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class ImportedAttribute
{
}
