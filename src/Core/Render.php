<?php declare(strict_types=1);

namespace Core;

use Slim\Views\Twig;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Render
{
    public static function config(): Twig
    {
        $twig = Twig::create(__DIR__ . '/../App/Views/', ['cache' => false]);

        $twig->getEnvironment()->addGlobal('is_hx_request', $_SERVER['HTTP_HX_REQUEST'] ?? false);
        $twig->getEnvironment()->addGlobal('cookie', $_COOKIE ?? false);

        $twig->getEnvironment()->addFilter(new TwigFilter('json_decode', function ($content) {
            return json_decode($content);
        }));

        $twig->getEnvironment()->addFunction(new TwigFunction('gen_uid', function ($prefix = 'q-admin-') {
            return uniqid($prefix);
        }));

        return $twig;
    }
}
