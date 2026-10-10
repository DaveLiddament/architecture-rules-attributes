<?php

declare(strict_types=1);

namespace DaveLiddament\Architecture\Attribute;

/**
 * Loads and stores entities, and hides how they are stored. That could be a database, files, an API, or data generated
 * at build time. The rest of the app never needs to know.
 *
 * When one entity owns others, such as an order and its lines, the repository loads and stores the owner, and the owned
 * entities come with it. There's no separate repository for order lines. In DDD terms, a repository works with
 * aggregate roots.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Repository
{
}
