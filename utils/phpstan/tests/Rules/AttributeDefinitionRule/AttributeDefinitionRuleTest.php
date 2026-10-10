<?php

declare(strict_types=1);

namespace Utils\Phpstan\Tests\Rules\AttributeDefinitionRule;

use DaveLiddament\PhpstanRuleTestHelper\AbstractRuleTestCase;
use PHPStan\Rules\Rule;
use Utils\Phpstan\Rules\AttributeDefinitionRule;

/** @extends AbstractRuleTestCase<AttributeDefinitionRule> */
final class AttributeDefinitionRuleTest extends AbstractRuleTestCase
{
    protected function getRule(): Rule
    {
        return new AttributeDefinitionRule(
            'Utils\Phpstan\Tests\Rules\AttributeDefinitionRule\Fixtures\Attribute',
            __DIR__.'/Fixtures/README.md',
        );
    }

    public function testDefinitions(): void
    {
        $this->assertIssuesReported(__DIR__.'/Fixtures/definitions.php');
    }

    public function testOutsideNamespace(): void
    {
        $this->assertIssuesReported(__DIR__.'/Fixtures/outsideNamespace.php');
    }
}
