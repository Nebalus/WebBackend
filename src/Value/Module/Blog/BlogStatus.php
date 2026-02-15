<?php

namespace Nebalus\Webapi\Value\Module\Blog;

enum BlogStatus: string
{
    case DRAFT = 'DRAFT';
    case PUBLISHED = 'PUBLISHED';
    case PRIVATE = 'PRIVATE';
}
