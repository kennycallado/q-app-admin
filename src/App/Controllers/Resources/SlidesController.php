<?php declare(strict_types=1);

namespace App\Controllers\Resources;

use App\Models\Repositories\SlidesRepository;
use App\Models\Slide;
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
        $slidesRepository = new SlidesRepository($i_db);

        $prepare = [
            'title' => 'Slides',
            'slides' => $slidesRepository->all()
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
        $slidesRepository = new SlidesRepository($i_db);

        $prepare = [
            'title' => 'media',
            'slide' => $slidesRepository->find($args['id']),
            'edit' => $request->getQueryParams()['edit'] ?? null,
            'no_header' => $this->no_header($request) ?? null  // comming from emia
        ];

        return $view->render($response, 'pages/entities/resources/slides/details.html', $prepare);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function store(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        global $app;

        $auth = $request->getAttribute('auth');
        $body = $request->getParsedBody();

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);

        // validation...
        $slide = array_slice($body, 1);
        $slide['id'] = $args['id'];
        $slide = new Slide(...$slide);

        $slidesRepository = new SlidesRepository($i_db);
        $slide = $slidesRepository->update($slide);

        // TODO: render show to avoid querying the database again

        $routeParser = $app->getRouteCollector()->getRouteParser();

        $header = $this->no_header($request) ? '?no_header=1' : '';
        $slides_url = $routeParser->urlFor('resources.slides.details', ['id' => $slide->id]) . $header;

        return $response->withHeader('Location', $slides_url);
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
        $slidesRepository = new SlidesRepository($i_db);

        $slidesRepository->delete($args['id']);

        $routeParser = $app->getRouteCollector()->getRouteParser();
        $slides_url = $routeParser->urlFor('resources.slides');

        return $response->withHeader('Location', $slides_url);
    }

    private function no_header(Request $request): bool
    {
        return isset($request->getQueryParams()['no_header']) ||
            !isset($request->getHeader('HX-Target')[0]) &&
            isset($request->getHeader('HX-Request')[0]);
    }
}
