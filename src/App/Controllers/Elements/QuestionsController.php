<?php declare(strict_types=1);

namespace App\Controllers\Elements;

use App\Models\Repositories\QuestionsRepository;
use App\Models\Question;
use Core\Utils\SurrealDB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

class QuestionsController
{
    /**
     * @param Group $group
     */
    public static function routes(Group $group): void
    {
        $group->get('', [self::class, 'index'])->setName('questions');
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
        $questionsRepository = new QuestionsRepository($i_db);

        $prepare = [
            'title' => 'Questions',
            'questions' => $questionsRepository->all()
        ];

        return $view->render($response, 'pages/entities/elements/questions/index.html', $prepare);
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
            'title' => 'Question',
            'type' => $request->getQueryParams()['type'] ?? null,
            'create' => true,
            'edit' => true
        ];

        return $view->render($response, 'pages/entities/elements/questions/details.html', $prepare);
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
        $questionsRepository = new QuestionsRepository($i_db);

        $prepare = [
            'title' => 'Question',
            'question' => $questionsRepository->find($args['id']),
            'edit' => $request->getQueryParams()['edit'] ?? null,
            'no_header' => $this->no_header($request) ?? null  // comming from emia
        ];

        return $view->render($response, 'pages/entities/elements/questions/details.html', $prepare);
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
        $questionsRepository = new QuestionsRepository($i_db);

        $question = new Question(...$body);
        $question = $questionsRepository->create($question);

        // TODO: render show to avoid querying the database again

        $routeParser = $app->getRouteCollector()->getRouteParser();
        $questions_url = $routeParser->urlFor('questions') . '/' . $question->id;

        return $response->withHeader('Location', $questions_url);
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

        // validation...
        $question = array_slice($body, 1);
        $question = new Question(...$question);

        $questionsRepository = new QuestionsRepository($i_db);
        $question = $questionsRepository->update($question);

        // TODO: render show to avoid querying the database again

        $routeParser = $app->getRouteCollector()->getRouteParser();

        $header = $this->no_header($request) ? '?no_header=1' : '';
        $questions_url = $routeParser->urlFor('questions') . '/' . $question->id . $header;

        return $response->withHeader('Location', $questions_url);
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
        $questionsRepository = new QuestionsRepository($i_db);

        $questionsRepository->delete($args['id']);

        $routeParser = $app->getRouteCollector()->getRouteParser();
        $questions_url = $routeParser->urlFor('questions');

        return $response->withHeader('Location', $questions_url);
    }

    private function no_header(Request $request): bool
    {
        return isset($request->getQueryParams()['no_header']) ||
            !isset($request->getHeader('HX-Target')[0]) &&
            isset($request->getHeader('HX-Request')[0]);
    }
}
