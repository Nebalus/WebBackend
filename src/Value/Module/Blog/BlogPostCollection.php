<?php

namespace Nebalus\Webapi\Value\Module\Blog;

use IteratorAggregate;
use Traversable;

class BlogPostCollection implements IteratorAggregate
{
    private array $blogPosts;

    private function __construct(BlogPost ...$blogPosts)
    {
        $this->blogPosts = $blogPosts;
    }

    public static function fromObjects(BlogPost ...$blogPosts): self
    {
        return new self(...$blogPosts);
    }

    public function toArray(): array
    {
        return $this->blogPosts;
    }

    public function getIterator(): Traversable
    {
        yield from $this->blogPosts;
    }
}