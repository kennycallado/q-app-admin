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
    /**
     * @param Group $group
     */
    public static function routes(Group $group): void
    {
        $group->get('', [self::class, 'index'])->setName('media');
        $group->post('', [self::class, 'store']);

        $group->get('/create', [self::class, 'create']);
        $group->post('/delete/{id}', [self::class, 'delete']);

        $group->get('/{id}', [self::class, 'show']);
        $group->patch('/{id}', [self::class, 'update']);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        $auth = $request->getAttribute('auth');

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        $mediaRepository = new MediaRepository($i_db);

        $prepare = [
            'title' => 'media',
            'media' => $mediaRepository->all()
        ];

        return $view->render($response, 'pages/elements/media/index.html', $prepare);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);

        $prepare = [
            'title' => 'media',
            'create' => true,
            'edit' => true
        ];

        return $view->render($response, 'pages/elements/media/details.html', $prepare);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $view = Twig::fromRequest($request);
        $auth = $request->getAttribute('auth');

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        $mediaRepository = new MediaRepository($i_db);

        $prepare = [
            'title' => 'media',
            'media' => $mediaRepository->find($args['id']),
            'edit' => $request->getQueryParams()['edit'] ?? null
        ];

        return $view->render($response, 'pages/elements/media/details.html', $prepare);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function store(Request $request, Response $response): Response
    {
        global $app;

        $auth = $request->getAttribute('auth');
        $body = $request->getParsedBody();

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        $mediaRepository = new MediaRepository($i_db);

        $media = new Media(...$body);
        $media = $mediaRepository->create($media);

        // TODO: render show to avoid querying the database again

        $routeParser = $app->getRouteCollector()->getRouteParser();
        $media_url = $routeParser->urlFor('media') . '/' . $media->id;

        return $response->withHeader('Location', $media_url);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function update(Request $request, Response $response): Response
    {
        global $app;

        $auth = $request->getAttribute('auth');
        $body = $request->getParsedBody();

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);

        $media = array_slice($body, 1);
        $media = new Media(...$media);

        $mediaRepository = new MediaRepository($i_db);
        $media = $mediaRepository->update($media);

        // TODO: render show to avoid querying the database again

        $routeParser = $app->getRouteCollector()->getRouteParser();
        $media_url = $routeParser->urlFor('media') . '/' . $media->id;

        return $response->withHeader('Location', $media_url);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args): Response
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
