<?php declare(strict_types=1);

namespace App\Controllers\Elements;

use App\Models\Repositories\MediaRepository;
use App\Models\Media;
use Core\Utils\SurrealDB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

class MediaController
{
    public static function routes(Group $group): void
    {
        $group->get('', [self::class, 'index'])->setName('media');
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
        $mediaRepository = new MediaRepository($i_db);

        $prepare = [
            'media' => $mediaRepository->all()
        ];

        return $view->render($response, 'pages/elements/media/index.html', $prepare);
    }

    public function create(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);

        return $view->render($response, 'pages/elements/media/create.html');
    }

    public function show(Request $request, Response $response, $args)
    {
        $view = Twig::fromRequest($request);
        $auth = $request->getAttribute('auth');

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        $mediaRepository = new MediaRepository($i_db);

        $prepare = [
            'media' => $mediaRepository->find($args['id']),
            'edit' => $request->getQueryParams()['edit'] ?? null
        ];

        return $view->render($response, 'pages/elements/media/details.html', $prepare);
    }

    public function store(Request $request, Response $response)
    {
        global $app;

        $auth = $request->getAttribute('auth');
        $body = $request->getParsedBody();

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        $mediaRepository = new MediaRepository($i_db);

        $media = new Media(...$body);
        $mediaRepository->create($media);

        $routeParser = $app->getRouteCollector()->getRouteParser();
        $media_url = $routeParser->urlFor('media');

        return $response->withHeader('Location', $media_url);
    }

    public function update(Request $request, Response $response, $args)
    {
        global $app;

        $auth = $request->getAttribute('auth');
        $body = $request->getParsedBody();

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        $mediaRepository = new MediaRepository($i_db);

        $media = new Media(...array_slice($body, 1));
        $mediaRepository->update($media);

        // TODO: render show to avoid querying the database again

        $routeParser = $app->getRouteCollector()->getRouteParser();
        $media_url = $routeParser->urlFor('media') . '/' . $args['id'];

        return $response->withHeader('Location', $media_url);
    }

    public function delete(Request $request, Response $response, $args)
    {
        global $app;

        $auth = $request->getAttribute('auth');

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        $mediaRepository = new MediaRepository($i_db);

        $mediaRepository->delete($args['id']);

        $routeParser = $app->getRouteCollector()->getRouteParser();
        $media_url = $routeParser->urlFor('media');

        return $response->withHeader('Location', $media_url);
    }
}
