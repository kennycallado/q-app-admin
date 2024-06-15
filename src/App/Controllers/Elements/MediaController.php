<?php declare(strict_types=1);

namespace App\Controllers\Elements;

use Core\Utils\SurrealDB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

class MediaController
{
    public static function routes(Group $group): void
    {
        // views
        $group->get('', [self::class, 'index'])->setName('media');
        // $group->get('/create', [self::class, 'create']);
        // $group->get('/edit/{id}',   [self::class, 'edit']);
        // $group->get('/{id}', [self::class, 'show']);

        // actions
        // $group->post('', [self::class, 'store']);
        // $group->patch('/{id}', [self::class, 'update']);
    }

    public function index(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);

        $prepare = [
            'media' => []
        ];

        return $view->render($response, 'pages/elements/media/index.html', $prepare);
    }
}
