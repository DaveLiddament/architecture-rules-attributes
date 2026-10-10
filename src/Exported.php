<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * Most applications are split into domains, such as `Walks`, `Billing` and `Stats`. A domain should keep its internals
 * private, so that other domains can't couple themselves to details that should be free to change. But some classes are
 * meant to be used by other domains: an entity, a value object, a service.
 *
 * `#[Exported]` marks those classes. A class in a domain can only be used by other domains if it is `#[Exported]`,
 * wherever it lives in that domain. With `to`, only the domains listed may use it. It isn't a role, so it goes
 * alongside one.
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
