<?php declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

class IndexController
{
    public static function routes(Group $group): void
    {
        $group->get('', [self::class, 'index'])->setName('home');
    }

    public function index(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);

        $prepare = [
            'first' => $request->getQueryParams()['first'] ?? null
        ];

        return $view->render($response, 'pages/home/index.html', $prepare);
    }
}
