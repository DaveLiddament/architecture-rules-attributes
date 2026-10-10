<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * The one place primitive configuration lives: API keys, URLs, limits, feature flags. Its properties are scalars, or
 * lists of scalars.
 *
 * Services don't take strings and integers in their constructors. They take a config provider. That keeps every setting
 * in one place you can find, and stops primitives leaking through the app.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class ConfigProvider
{
}
