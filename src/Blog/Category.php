<?php

declare(strict_types=1);

namespace App\Blog;

/**
 * A category label together with its URL slug and the number of articles in it.
 */
final readonly class Category
{
    public function __construct(
        public string $name,
        public string $slug,
        public int $count,
    ) {
    }

    /**
     * URL slug for a category label, e.g. "AI Engineering" -> "ai-engineering".
     */
    public static function slugify(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';

        return trim($slug, '-');
    }
}
