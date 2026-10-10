<?php

declare(strict_types=1);

namespace Utils\Phpstan\Tests\Rules\AttributeConventionsRule\Fixtures\Attribute;

// Valid

/**
 * An attribute with no arguments.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Valid
{
}

/** A one-line docblock on a single line is fine. */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class ValidSingleLineDocblock
{
}

/**
 * A description can run over several lines,
 * to explain fully what the attribute means.
 *
 * And over several paragraphs.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class ValidMultiLineDocblock
{
}

/**
 * A description, then a tag.
 *
 * @internal
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class ValidDocblockWithTag
{
}

/**
 * An attribute with a readonly promoted property.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class ValidWithArgument
{
    /**
     * @param list<string>|null $to the domains
     */
    public function __construct(
        public readonly ?array $to = null,
    ) {
    }
}

/**
 * A readonly class, so its promoted properties are readonly.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class ValidReadonlyClass
{
    public function __construct(
        public string $name,
    ) {
    }
}

// Not final

/**
 * Not final.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class NotFinal // ERROR Attribute #[NotFinal] must be final.
{
}

/**
 * Abstract, so not final.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
abstract class NotFinalAbstract // ERROR Attribute #[NotFinalAbstract] must be final.
{
}

// Wrong declaration

/**
 * Not declared as an attribute.
 */
final class NotAnAttribute // ERROR Attribute #[NotAnAttribute] must be declared with #[\Attribute(\Attribute::TARGET_CLASS)]: classes only, not repeatable.
{
}

/**
 * Targets everything (the default).
 */
#[\Attribute]
final class TargetsAll // ERROR Attribute #[TargetsAll] must be declared with #[\Attribute(\Attribute::TARGET_CLASS)]: classes only, not repeatable.
{
}

/**
 * Targets methods.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final class TargetsMethod // ERROR Attribute #[TargetsMethod] must be declared with #[\Attribute(\Attribute::TARGET_CLASS)]: classes only, not repeatable.
{
}

/**
 * Targets classes and methods.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
final class TargetsClassAndMethod // ERROR Attribute #[TargetsClassAndMethod] must be declared with #[\Attribute(\Attribute::TARGET_CLASS)]: classes only, not repeatable.
{
}

/**
 * Repeatable.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class Repeatable // ERROR Attribute #[Repeatable] must be declared with #[\Attribute(\Attribute::TARGET_CLASS)]: classes only, not repeatable.
{
}

// Docblock

#[\Attribute(\Attribute::TARGET_CLASS)]
final class NoDocblock // ERROR Attribute #[NoDocblock] must have a docblock explaining what it means.
{
}

/*
 * A comment, not a docblock.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class CommentNotDocblock // ERROR Attribute #[CommentNotDocblock] must have a docblock explaining what it means.
{
}

/**
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class EmptyDocblock // ERROR Attribute #[EmptyDocblock] must have a docblock explaining what it means.
{
}

/**
 * @internal
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class TagOnlyDocblock // ERROR Attribute #[TagOnlyDocblock] must have a docblock explaining what it means.
{
}

/**
 * @internal
 *
 * A description after a tag is part of the tag, so doesn't count.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class DescriptionAfterTag // ERROR Attribute #[DescriptionAfterTag] must have a docblock explaining what it means.
{
}

// Properties

/**
 * A property that isn't promoted.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class NonPromotedProperty
{
    public readonly string $name; // ERROR Property $name of attribute #[NonPromotedProperty] must be a readonly promoted constructor property.

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}

/**
 * A promoted property that isn't readonly.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class PromotedNotReadonly
{
    public function __construct(
        public ?string $name = null, // ERROR Property $name of attribute #[PromotedNotReadonly] must be a readonly promoted constructor property.
    ) {
    }
}

/**
 * Two properties: the first is fine, the second isn't readonly.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class SecondPropertyNotReadonly
{
    public function __construct(
        public readonly string $first,
        public string $second, // ERROR Property $second of attribute #[SecondPropertyNotReadonly] must be a readonly promoted constructor property.
    ) {
    }
}

// README

/**
 * Not mentioned in the README.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Undocumented // ERROR Attribute #[Undocumented] is not documented in the README: it must mention #[Undocumented].
{
}

/**
 * The README only mentions #[DocumentedWithArguments(...)], which counts.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class DocumentedWithArguments
{
}

/**
 * The README mentions this name, but not as #[MentionedWithoutBrackets].
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class MentionedWithoutBrackets // ERROR Attribute #[MentionedWithoutBrackets] is not documented in the README: it must mention #[MentionedWithoutBrackets].
{
}

// Not checked

/**
 * An anonymous class is not an attribute.
 */
$anonymous = new class {
    public string $name = 'not checked';
};

/**
 * Interfaces, traits and enums aren't checked.
 */
interface NotAClass
{
}
