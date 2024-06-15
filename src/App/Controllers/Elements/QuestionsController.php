<?php declare(strict_types=1);

namespace App\Controllers\Elements;

use Core\Utils\SurrealDB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

class QuestionsController
{
    public static function routes(Group $group): void
    {
        // actions
        $group->get('', [self::class, 'index'])->setName('questions');
        // $group->post('',       [self::class, 'store']);
        // $group->patch('/{id}', [self::class, 'update']);

        // views
        // $group->get('/edit/{id}',   [self::class, 'edit']);
        $group->get('/create', [self::class, 'create']);
        $group->get('/{id}', [self::class, 'show']);
    }

    public function index(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);
        $auth = $request->getAttribute('auth');

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);

        $sql = 'SELECT * FROM questions;';
        $db_res = $i_db->rawQuery($sql);

        $prepare = [
            'title' => 'Questions',
            'questions' => $db_res[0]->result
        ];

        return $view->render($response, 'pages/elements/questions/index.html', $prepare);
    }

    public function show(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);
        $auth = $request->getAttribute('auth');

        $id = $request->getAttribute('id');

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);

        $sql = "SELECT * FROM $id;";
        $db_res = $i_db->rawQuery($sql);

        $prepare = [
            'title' => 'Question',
            'question' => $db_res[0]->result[0]
        ];

        return $view->render($response, 'pages/elements/questions/show.html', $prepare);
    }

    public function create(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);
        $type = $request->getQueryParams()['type'] ?? null;

        $prepare = [
            'title' => 'Create Question',
            'type' => $type
        ];

        switch ($type) {
            case 'text':
                return $view->render($response, 'pages/elements/questions/create.html', $prepare);
                break;
            case 'range':
                return $view->render($response, 'pages/elements/questions/create.html', $prepare);
                break;
            default:
                // error 404
                break;
        }
    }
}
