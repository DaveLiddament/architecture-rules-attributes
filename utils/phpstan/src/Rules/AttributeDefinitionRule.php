<?php

declare(strict_types=1);

namespace Utils\Phpstan\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Checks every attribute's docblock matches its definition in the README, so the two can't drift apart.
 *
 * A definition is the text between `<!-- definition: Name -->` and `<!-- /definition -->`. The docblock's description
 * must have the same paragraphs. Line wrapping doesn't matter, and links are compared as their text.
 *
 * @implements Rule<InClassNode>
 */
final class AttributeDefinitionRule implements Rule
{
    /** @var array<string, string>|null attribute name => normalised definition */
    private ?array $definitions = null;

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
        $definition = $this->definitions()[$name] ?? null;

        if (null === $definition) {
            return [$this->error(
                "Attribute #[{$name}] has no definition in the README. Mark one with <!-- definition: {$name} --> and <!-- /definition -->.",
                $class,
            )];
        }

        if ($definition !== self::normalise($this->docblockDescription($class))) {
            return [$this->error(
                "The docblock of attribute #[{$name}] doesn't match its definition in the README. It must have the same paragraphs, with links as plain text.",
                $class,
            )];
        }

        return [];
    }

    /** @return array<string, string> */
    private function definitions(): array
    {
        if (null !== $this->definitions) {
            return $this->definitions;
        }

        $readme = is_file($this->readmePath) ? file_get_contents($this->readmePath) : false;
        preg_match_all(
            '/<!-- definition: (\w+) -->(.*?)<!-- \/definition -->/s',
            false === $readme ? '' : $readme,
            $matches,
            \PREG_SET_ORDER,
        );

        $this->definitions = [];
        foreach ($matches as [, $name, $text]) {
            $this->definitions[$name] ??= self::normalise($text);
        }

        return $this->definitions;
    }

    /**
     * The docblock's text before any tags, without the comment markers.
     */
    private function docblockDescription(Class_ $class): string
    {
        $docComment = $class->getDocComment();
        if (null === $docComment) {
            return '';
        }

        $lines = [];
        foreach (explode("\n", substr(trim($docComment->getText()), 3, -2)) as $line) {
            $text = trim(ltrim(trim($line), '*'));
            if (str_starts_with($text, '@')) {
                break;
            }

            $lines[] = $text;
        }

        return implode("\n", $lines);
    }

    /**
     * Paragraphs separated by a blank line, each on one line, with Markdown links replaced by their text.
     */
    private static function normalise(string $text): string
    {
        $withoutLinks = preg_replace('/\[((?:[^\[\]]|\[[^\[\]]*\])*)\]\([^)\s]*\)/', '$1', $text) ?? $text;
        $paragraphs = preg_split('/\n\s*\n/', trim($withoutLinks));
        if (false === $paragraphs) {
            return '';
        }

        return implode("\n\n", array_map(
            static fn (string $paragraph): string => preg_replace('/\s+/', ' ', trim($paragraph)) ?? $paragraph,
            $paragraphs,
        ));
    }

    private function error(string $message, Class_ $class): IdentifierRuleError
    {
        return RuleErrorBuilder::message($message)
            ->identifier('architectureAttribute.definition')
            ->line($class->name?->getStartLine() ?? $class->getStartLine())
            ->build();
    }
}
