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
            'cache' => false, // TODO needs to create an cache directory in the container
            'strict_variables' => true,
            'autoescape' => 'html',
        ]);
    }
}
