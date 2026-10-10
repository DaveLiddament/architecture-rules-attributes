<?php

declare(strict_types=1);

namespace Utils\Phpstan\Tests\Rules\AttributeDefinitionRule\Fixtures\Attribute;

// Matches

/**
 * A single paragraph that matches.
 */
final class Matches
{
}

/**
 * A paragraph wrapped
 * differently from the README.
 */
final class DifferentWrapping
{
}

/**
 * The first paragraph.
 *
 * The second paragraph.
 */
final class MultipleParagraphs
{
}

/**
 * Loaded through a `#[Other]`, written as a link in the README.
 */
final class LinkAsText
{
}

/**
 * A description that matches, then a tag.
 *
 * @internal
 */
final class MatchesThenTag
{
}

/**
 * Only the first definition counts.
 */
final class DefinedTwice
{
}

/**
 * Text outside the markers isn't part of the definition.
 */
final class OnlyMarkedText
{
}

// Doesn't match

/**
 * A word that is changed.
 */
final class WordChanged // ERROR The docblock of attribute #[WordChanged] doesn't match its definition in the README. It must have the same paragraphs, with links as plain text.
{
}

/**
 * The first paragraph.
 */
final class MissingParagraph // ERROR The docblock of attribute #[MissingParagraph] doesn't match its definition in the README. It must have the same paragraphs, with links as plain text.
{
}

/**
 * The first sentence. The second sentence.
 */
final class ParagraphsJoined // ERROR The docblock of attribute #[ParagraphsJoined] doesn't match its definition in the README. It must have the same paragraphs, with links as plain text.
{
}

/**
 * @internal
 */
final class TagOnly // ERROR The docblock of attribute #[TagOnly] doesn't match its definition in the README. It must have the same paragraphs, with links as plain text.
{
}

final class NoDocblock // ERROR The docblock of attribute #[NoDocblock] doesn't match its definition in the README. It must have the same paragraphs, with links as plain text.
{
}

// No definition

/**
 * Not in the README.
 */
final class NoDefinition // ERROR Attribute #[NoDefinition] has no definition in the README. Mark one with <!-- definition: NoDefinition --> and <!-- /definition -->.
{
}

/**
 * The README's definition is never closed.
 */
final class UnclosedDefinition // ERROR Attribute #[UnclosedDefinition] has no definition in the README. Mark one with <!-- definition: UnclosedDefinition --> and <!-- /definition -->.
{
}

// Not checked

/**
 * An anonymous class is not an attribute.
 */
$anonymous = new class {
};

/**
 * Interfaces aren't checked.
 */
interface NotAClass
{
}
