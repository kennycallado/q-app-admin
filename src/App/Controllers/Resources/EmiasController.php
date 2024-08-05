<?php declare(strict_types=1);

namespace App\Controllers\Resources;

use Core\Utils\SurrealDB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

class EmiasController
{
    /**
     * @param Group $group
     */
    public static function routes(Group $group): void
    {
        $group->get('', [self::class, 'index'])->setName('resources.emias');

        /*
         * $group->post('', [self::class, 'store']);
         *
         * $group->get('/create', [self::class, 'create'])->setName('resources.emias.create');
         * $group->post('/delete/{id}', [self::class, 'delete'])->setName('resources.emias.delete');
         *
         * $group->get('/{id}', [self::class, 'show'])->setName('resources.emias.details');
         * $group->patch('/{id}', [self::class, 'update']);
         */
    }

    public function index(Request $request, Response $response): Response
    {
        $test = [
            'id' => 'emias:e1',
            'ref' => 'emia-uno',
            'description' => 'Emia 1 description',
            'slides' => [
                'slides:s1',
                'slides:s2',
                'slides:s3'
            ]
        ];

        $view = Twig::fromRequest($request);
        $auth = $request->getAttribute('auth');

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        // $slidesRepository = new SlidesRepository($i_db);

        $prepare = [
            'title' => 'Emia',
            'emia' => $test
            // 'slides' => $slidesRepository->all()
        ];

        return $view->render($response, 'pages/entities/resources/emias/show.html', $prepare);
    }
}
