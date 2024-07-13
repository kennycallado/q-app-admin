<?php declare(strict_types=1);

namespace App\Controllers\Resources;

use Core\Utils\SurrealDB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

class SlidesController
{
    /**
     * @param Group $group
     */
    public static function routes(Group $group): void
    {
        $group->get('', [self::class, 'index'])->setName('resources.slides');
        $group->post('', [self::class, 'store']);

        $group->get('/create', [self::class, 'create'])->setName('resources.slides.create');
        $group->post('/delete/{id}', [self::class, 'delete'])->setName('resources.slides.delete');

        $group->get('/{id}', [self::class, 'show'])->setName('resources.slides.show');
        $group->patch('/{id}', [self::class, 'update']);
    }

    public function index(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        $auth = $request->getAttribute('auth');

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        // $slidesRepository = new slidesRepository($i_db);

        // TODO: implement repository
        $db_res = $i_db->rawQuery('SELECT * FROM slides;');
        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            return $view->render($response, 'pages/entities/resources/slides/index.html');
        }

        $prepare = [
            'title' => 'slides',
            'slides' => $db_res[0]->result
        ];

        return $view->render($response, 'pages/entities/resources/slides/index.html', $prepare);
    }

    public function create(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);

        $prepare = [
            'title' => 'slides',
            'create' => true,
            'edit' => true,
            'no_header' => $this->no_header($request)
        ];

        return $view->render($response, 'pages/entities/resources/slides/details.html', $prepare);
    }

    /**
     * @param array $args
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $view = Twig::fromRequest($request);
        $auth = $request->getAttribute('auth');

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);

        // TODO: implement repository
        $db_res = $i_db->rawQuery('SELECT * FROM slides;');
        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            return $view->render($response, 'pages/resources/slides/index.html');
        }

        $prepare = [
            'title' => 'media',
            'slide' => $db_res[0]->result[0],
            'edit' => $request->getQueryParams()['edit'] ?? null,
            'no_header' => $this->no_header($request) ?? null  // comming from emia
        ];

        return $view->render($response, 'pages/entities/resources/slides/details.html', $prepare);
    }

    private function no_header(Request $request): bool
    {
        return isset($request->getQueryParams()['no_header']) ||
            !isset($request->getHeader('HX-Target')[0]) &&
            isset($request->getHeader('HX-Request')[0]);
    }
}
