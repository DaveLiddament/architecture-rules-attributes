<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * A snapshot handed to a template. Like a value object, but it holds formatted, ready-to-display values rather than
 * domain values, so the template has no decisions left to make.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class ViewModel
{
}
