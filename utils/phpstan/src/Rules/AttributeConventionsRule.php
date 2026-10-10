<?php

declare(strict_types=1);

namespace Utils\Phpstan\Rules;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Checks every attribute class follows the package's conventions: final, classes only, a docblock explaining what it
 * means, readonly promoted properties, and documented in the README.
 *
 * @implements Rule<InClassNode>
 */
final class AttributeConventionsRule implements Rule
{
    private ?string $readme = null;

    public function __construct(
        private readonly string $namespace,
        private readonly string $readmePath,
    ) {
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /** @return list<IdentifierRuleError> */
    public function processNode(Node $node, Scope $scope): array
    {
        $class = $node->getOriginalNode();
        $classReflection = $node->getClassReflection();

        if (!$class instanceof Class_ || null === $class->name || $classReflection->isAnonymous()) {
            return [];
        }

        if (!str_starts_with($classReflection->getName(), $this->namespace.'\\')) {
            return [];
        }

        $name = $class->name->toString();
        $line = $class->name->getStartLine();
        $errors = [];

        if (!$class->isFinal()) {
            $errors[] = $this->error("Attribute #[{$name}] must be final.", 'final', $line);
        }

        if (!$this->isDeclaredForClassesOnly($classReflection)) {
            $errors[] = $this->error(
                "Attribute #[{$name}] must be declared with #[\\Attribute(\\Attribute::TARGET_CLASS)]: classes only, not repeatable.",
                'declaration',
                $line,
            );
        }

        if (!$this->hasDocblockDescription($class)) {
            $errors[] = $this->error(
                "Attribute #[{$name}] must have a docblock explaining what it means.",
                'docblock',
                $line,
            );
        }

        foreach ($this->invalidProperties($class) as $propertyName => $propertyLine) {
            $errors[] = $this->error(
                "Property \${$propertyName} of attribute #[{$name}] must be a readonly promoted constructor property.",
                'property',
                $propertyLine,
            );
        }

        if (!$this->isDocumented($name)) {
            $errors[] = $this->error(
                "Attribute #[{$name}] is not documented in the README: it must mention #[{$name}].",
                'readme',
                $line,
            );
        }

        return $errors;
    }

    private function isDeclaredForClassesOnly(ClassReflection $classReflection): bool
    {
        $declarations = array_values(array_filter(
            $classReflection->getAttributes(),
            static fn ($attribute): bool => \Attribute::class === $attribute->getName(),
        ));

        if (1 !== count($declarations)) {
            return false;
        }

        $flags = $declarations[0]->getArgumentTypes()['flags'] ?? null;

        return null !== $flags && [\Attribute::TARGET_CLASS] === $flags->getConstantScalarValues();
    }

    /**
     * The docblock has at least one line of description before any tags.
     */
    private function hasDocblockDescription(Class_ $class): bool
    {
        $docComment = $class->getDocComment();
        if (null === $docComment) {
            return false;
        }

        $contents = substr(trim($docComment->getText()), 3, -2);
        foreach (explode("\n", $contents) as $line) {
            $text = trim(ltrim(trim($line), '*'));
            if (str_starts_with($text, '@')) {
                return false;
            }

            if ('' !== $text) {
                return true;
            }
        }

        return false;
    }

    /**
     * Properties that aren't readonly promoted constructor properties.
     *
     * @return array<string, int> property name => line
     */
    private function invalidProperties(Class_ $class): array
    {
        $invalid = [];

        foreach ($class->stmts as $stmt) {
            if ($stmt instanceof Property) {
                foreach ($stmt->props as $property) {
                    $invalid[$property->name->toString()] = $property->getStartLine();
                }
            }

            if (!$stmt instanceof ClassMethod || '__construct' !== $stmt->name->toLowerString()) {
                continue;
            }

            foreach ($stmt->params as $param) {
                $isReadonly = $class->isReadonly() || 0 !== ($param->flags & Modifiers::READONLY);
                if (!$param->isPromoted() || $isReadonly || !$param->var instanceof Variable || !is_string($param->var->name)) {
                    continue;
                }

                $invalid[$param->var->name] = $param->getStartLine();
            }
        }

        return $invalid;
    }

    private function isDocumented(string $name): bool
    {
        if (null === $this->readme) {
            $contents = is_file($this->readmePath) ? file_get_contents($this->readmePath) : false;
            $this->readme = false === $contents ? '' : $contents;
        }

        return str_contains($this->readme, "#[{$name}]") || str_contains($this->readme, "#[{$name}(");
    }

    private function error(string $message, string $check, int $line): IdentifierRuleError
    {
        return RuleErrorBuilder::message($message)
            ->identifier('architectureAttribute.'.$check)
            ->line($line)
            ->build();
    }
}
