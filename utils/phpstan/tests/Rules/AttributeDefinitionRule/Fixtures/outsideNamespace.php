<?php

declare(strict_types=1);

namespace Utils\Phpstan\Tests\Rules\AttributeDefinitionRule\Fixtures\Other;

// Classes outside the configured namespace are not checked.

final class NoDefinitionOutsideNamespace
{
}
