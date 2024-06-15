<?php declare(strict_types=1);

namespace App\Controllers\Elements;

use App\Models\Repositories\ParagraphsRepository;
use App\Models\Paragraph;
use Core\Utils\SurrealDB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

class ParagraphsController
{
    public static function routes(Group $group): void
    {
        // views
        $group->get('', [self::class, 'index'])->setName('paragraphs');
        $group->get('/create', [self::class, 'create']);
        // $group->get('/edit/{id}',   [self::class, 'edit']);
        $group->get('/{id}', [self::class, 'show']);

        // actions
        $group->post('', [self::class, 'store']);
        // $group->patch('/{id}', [self::class, 'update']);
    }

    public function index(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);
        $auth = $request->getAttribute('auth');
        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        $paragraphsRepository = new ParagraphsRepository($i_db);

        $paragraphs = $paragraphsRepository->all();

        $prepare = [
            'paragraphs' => $paragraphs
        ];

        return $view->render($response, 'pages/elements/paragraphs/index.html', $prepare);
    }

    public function create(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);

        return $view->render($response, 'pages/elements/paragraphs/create.html');
    }

    public function show(Request $request, Response $response, $args)
    {
        $view = Twig::fromRequest($request);
        $auth = $request->getAttribute('auth');
        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        $paragraphsRepository = new ParagraphsRepository($i_db);

        $paragraph = $paragraphsRepository->find($args['id']);

        $prepare = [
            'paragraph' => $paragraph
        ];

        return $view->render($response, 'pages/elements/paragraphs/show.html', $prepare);
    }

    public function store(Request $request, Response $response)
    {
        global $app;

        $auth = $request->getAttribute('auth');
        $body = $request->getParsedBody();

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        $paragraphsRepository = new ParagraphsRepository($i_db);

        $paragraph = new Paragraph(null, $body['ref'], $body['type'], $body['content']);
        $db_res = $paragraphsRepository->create($paragraph);

        // TODO: redirec to show

        $routeParser = $app->getRouteCollector()->getRouteParser();
        $paragraphs_url = $routeParser->urlFor('paragraphs');

        return $response->withHeader('Location', $paragraphs_url);
    }
}
