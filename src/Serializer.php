<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * Converts to and from wire formats such as JSON, CSV or an API's payloads. It's the one place array shapes
 * (`array{...}`) belong: everywhere else, structured data travels as a value object, DTO or view model.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Serializer
{
}
