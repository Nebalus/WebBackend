<?php

namespace Nebalus\Webapi\Factory;

use Twig\Environment as Twig;
use Twig\Loader\FilesystemLoader;

readonly class TwigFactory
{
    public function __construct()
    {
    }

    public function __invoke(): Twig
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        return new Twig($loader, [
            'cache' => false,
            'strict_variables' => true,
            'autoescape' => 'html',
        ]);
    }
}
