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
        $group->get('', [self::class, 'index'])->setName('paragraphs');
        $group->post('', [self::class, 'store']);

        $group->get('/create', [self::class, 'create']);
        $group->post('/delete/{id}', [self::class, 'delete']);

        $group->get('/{id}', [self::class, 'show']);
        $group->patch('/{id}', [self::class, 'update']);
    }

    public function index(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);
        $auth = $request->getAttribute('auth');

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        $paragraphsRepository = new ParagraphsRepository($i_db);

        $prepare = [
            'paragraphs' => $paragraphsRepository->all()
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

        $prepare = [
            'paragraph' => $paragraphsRepository->find($args['id']),
            'edit' => $request->getQueryParams()['edit'] ?? null
        ];

        return $view->render($response, 'pages/elements/paragraphs/details.html', $prepare);
    }

    public function store(Request $request, Response $response)
    {
        global $app;

        $auth = $request->getAttribute('auth');
        $body = $request->getParsedBody();

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        $paragraphsRepository = new ParagraphsRepository($i_db);

        $paragraph = new Paragraph(...$body);
        $paragraphsRepository->create($paragraph);

        // TODO: render show to avoid querying the database again

        $routeParser = $app->getRouteCollector()->getRouteParser();
        $paragraphs_url = $routeParser->urlFor('paragraphs');

        return $response->withHeader('Location', $paragraphs_url);
    }

    public function update(Request $request, Response $response, $args)
    {
        global $app;

        $auth = $request->getAttribute('auth');
        $body = $request->getParsedBody();

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        $paragraphsRepository = new ParagraphsRepository($i_db);

        $paragraph = new Paragraph(...array_slice($body, 1));
        $paragraphsRepository->update($paragraph);

        // TODO: render show to avoid querying the database again

        $routeParser = $app->getRouteCollector()->getRouteParser();
        $paragraphs_url = $routeParser->urlFor('paragraphs') . '/' . $args['id'];

        return $response->withHeader('Location', $paragraphs_url);
    }

    public function delete(Request $request, Response $response, $args)
    {
        global $app;

        $auth = $request->getAttribute('auth');

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        $paragraphsRepository = new ParagraphsRepository($i_db);

        $paragraphsRepository->delete($args['id']);

        $routeParser = $app->getRouteCollector()->getRouteParser();
        $paragraphs_url = $routeParser->urlFor('paragraphs');

        return $response->withHeader('Location', $paragraphs_url);
    }
}
