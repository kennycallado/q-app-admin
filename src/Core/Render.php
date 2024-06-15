<?php declare(strict_types=1);

namespace Core;

use Slim\Views\Twig;

class Render
{
    public static function config(): Twig
    {
        $twig = Twig::create(__DIR__ . '/../App/Views/', ['cache' => false]);

        $twig->getEnvironment()->addGlobal('_hx_request', $_SERVER['HTTP_HX_REQUEST'] ?? false);
        $twig->getEnvironment()->addGlobal('cookie', $_COOKIE ?? false);

        return $twig;
    }
}
