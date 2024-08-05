<?php declare(strict_types=1);

namespace App\Controllers\Settings;

use App\Models\Repositories\UserRepository;
use Core\Utils\SurrealDB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

class UserSettingsController
{
    /**
     * @param Group $group
     */
    public static function routes(Group $group): void
    {
        // $group->get('', [self::class, 'index'])->setName('settings.user');
        // $group->post('', [self::class, 'store']);
        // $group->get('/create', [self::class, 'create']);
        // $group->post('/delete/{id}', [self::class, 'delete']);
        // $group->patch('/username/{id}', [self::class, 'patch_username']);

        $group->get('/{id}', [self::class, 'show'])->setName('settings.user');
        $group->patch('/{id}/project', [self::class, 'patch_project']);
    }

    /**
     * public function index(Request $request, Response $response): Response
     * {
     *     $view = Twig::fromRequest($request);
     *     $auth = $request->getAttribute('auth');
     *
     *     $i_db = new SurrealDB('global', 'main', $auth->g_auth);
     *
     *     $prepare = [
     *         'title' => 'users',
     *         'users' => []
     *     ];
     *
     *     return $view->render($response, 'pages/settings/user.html', $prepare);
     * }
     */

    /**
     * @param array $args
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $view = Twig::fromRequest($request);
        $auth = $request->getAttribute('auth');

        $g_db = new SurrealDB('global', 'main', $auth->g_auth);

        $query = 'SELECT *, (SELECT id, name FROM projects WHERE center = $parent.id) AS projects FROM centers;';
        $db_res = $g_db->rawQuery($query);
        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            // it's an internal error
            return $view->render($response->withStatus(500), 'pages/auth/select.html', ['error' => $db_res->details]);
            // return $view->render($response->withStatus($res_db->code), 'pages/auth/select.html', ['error' => $res_db->information]);
        }

        $prepare = [
            'centers' => $db_res[0]->result
        ];

        return $view->render($response, 'pages/settings/user.html', $prepare);
    }

    /**
     * @param array $args
     */
    public function patch_project(Request $request, Response $response, array $args): Response
    {
        global $app;

        $view = Twig::fromRequest($request);
        $auth = $request->getAttribute('auth');

        $g_db = new SurrealDB('global', 'main', $auth->g_auth);

        $body = (object) array_slice($request->getParsedBody(), 1);
        $userRepository = new UserRepository($g_db);

        $query = 'SELECT *, (SELECT id, name FROM projects WHERE center = $parent.id) AS projects FROM centers;';
        $query .= "UPDATE $auth->user_id MERGE { project: $body->project };";

        $prepare = [];

        $db_res = $g_db->rawQuery($query);
        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            // TODO: internal error
        } else
            $prepare['centers'] = $db_res[0]->result;

        if ($db_res[1]->status !== 'OK') {
            $prepare['error'] = 'You are not authorized to access this project';

            return $view->render($response->withStatus(401), 'pages/settings/user.html', $prepare);
        }

        try {
            $auth = $auth->refresh();
        } catch (\Exception $e) {
            $prepare['error'] = $e->getMessage();

            return $view->render($response->withStatus(500), 'pages/settings/user.html', $prepare);
        }

        // $auth->unset_cookies();
        $auth->set_cookies();
        $_SESSION['auth'] = $auth;

        $routeParser = $app->getRouteCollector()->getRouteParser();
        // $show_url = $routeParser->urlFor('settings.user') . '/' . $auth->user_id;
        $show_url = '/settings/user/' . $auth->user_id;

        return $response->withHeader('Location', $show_url)->withStatus(302);
    }
}
