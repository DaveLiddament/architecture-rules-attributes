<?php

declare(strict_types=1);

namespace Utils\Phpstan\Tests\Rules\AttributeConventionsRule;

use DaveLiddament\PhpstanRuleTestHelper\AbstractRuleTestCase;
use PHPStan\Rules\Rule;
use Utils\Phpstan\Rules\AttributeConventionsRule;

/** @extends AbstractRuleTestCase<AttributeConventionsRule> */
final class AttributeConventionsRuleTest extends AbstractRuleTestCase
{
    protected function getRule(): Rule
    {
        return new AttributeConventionsRule(
            'Utils\Phpstan\Tests\Rules\AttributeConventionsRule\Fixtures\Attribute',
            __DIR__.'/Fixtures/README.md',
        );
    }

    public function testAttributes(): void
    {
        $this->assertIssuesReported(__DIR__.'/Fixtures/attributes.php');
    }

    public function testImportedAttribute(): void
    {
        $this->assertIssuesReported(__DIR__.'/Fixtures/importedAttribute.php');
    }

    public function testOutsideNamespace(): void
    {
        $this->assertIssuesReported(__DIR__.'/Fixtures/outsideNamespace.php');
    }
}
