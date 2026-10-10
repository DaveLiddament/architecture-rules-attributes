<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * The class may be used by other domains. With `to`, only by the domains listed.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Exported
{
    /**
     * @param non-empty-list<string>|null $to the domains that may use the class, or null for every domain
     */
    public function __construct(
        public readonly ?array $to = null,
    ) {
    }
}
