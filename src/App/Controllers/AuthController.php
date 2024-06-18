<?php declare(strict_types=1);

namespace App\Controllers;

use Core\Middleware\AuthMiddleware;
use Core\Utils\Auth;
use Core\Utils\SurrealDB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;
use Exception;

class AuthController
{
    public static function routes(Group $group): void
    {
        $group->get('/login', [self::class, 'get_index'])->setName('login');
        $group->post('/login', [self::class, 'post_index']);

        $group->patch('/select', [self::class, 'patch_select'])->setName('login_select');

        $group->post('/signup', [self::class, 'post_signup'])->setName('signup');
        $group->get('/logout', [self::class, 'logout'])->setName('logout')->add(AuthMiddleware::class);
    }

    public function get_index(Request $request, Response $response, $args)
    {
        $view = Twig::fromRequest($request);

        return $view->render($response, 'pages/auth/index.html');
    }

    public function post_index(Request $request, Response $response, $args)
    {
        global $app;

        // TODO: $auth = new Auth($_ENV['AUTH_URL']);
        $auth = new Auth('http://auth:9000/auth');

        $view = Twig::fromRequest($request);
        $body = (object) $request->getParsedBody();

        if (empty($body->username) || empty($body->password)) {
            return $view->render($response->withStatus(400), 'pages/auth/index.html', ['error' => 'Username and password are required']);
        }

        try {
            $auth = $auth->signin($body->username, $body->password);
        } catch (Exception $e) {
            // probably username or password is wrong
            // return response with status 401
            return $view->render($response->withStatus(401), 'pages/auth/index.html', ['error' => $e->getMessage()]);
        }

        if (!isset($auth->role) || !isset($auth->project) || !isset($auth->p_auth)) {
            $surreal = new SurrealDB('global', 'main', $auth->g_auth);

            $sql = 'SELECT *, (SELECT id, name FROM projects WHERE center = $parent.id) AS projects FROM centers;';

            $res_db = $surreal->rawQuery($sql);
            if (isset($res_db->code)) {  // error in query
                // it's an internal error
                return $view->render($response->withStatus(500), 'pages/auth/select.html', ['error' => $res_db->details]);
                // return $view->render($response->withStatus($res_db->code), 'pages/auth/select.html', ['error' => $res_db->information]);
            }

            $centers = $res_db[0]->result;

            $prepare = [
                'centers' => $centers
            ];

            $_SESSION['pre_auth'] = $auth;
            $_SESSION['centers'] = $centers;
            return $view->render($response, 'pages/auth/select.html', $prepare);  // TODO: ?? should redirect
        }

        $this->set_cookies($auth, $body->username);

        $_SESSION['auth'] = $auth;

        $routeParser = $app->getRouteCollector()->getRouteParser();
        $home_url = $routeParser->urlFor('home') . '?first=true';

        // $response = $app->getResponseFactory()->createResponse();

        return $response->withHeader('Location', $home_url)->withStatus(302);
    }

    public function patch_select(Request $request, Response $response, $args)
    {
        global $app;

        /** @var Auth $pre_auth */
        $pre_auth = $_SESSION['pre_auth'];
        $centers = $_SESSION['centers'];

        $surreal = new SurrealDB('global', 'main', $pre_auth->g_auth);
        $view = Twig::fromRequest($request);
        $body = (object) $request->getParsedBody();

        $sql = "UPDATE $pre_auth->user_id SET project = $body->project;";
        $res_db = $surreal->rawQuery($sql);

        // error query
        if (isset($res_db->code)) {
            // update failed
            return $view->render($response->withStatus(500), 'pages/auth/select.html', ['centers' => $centers, 'error' => $res_db->details]);
        }

        // probably no authorization
        if (isset($res_db[0]->status) && $res_db[0]->status === 'ERR') {
            return $view->render($response->withStatus(401), 'pages/auth/select.html', ['centers' => $centers, 'error' => 'You are not authorized to access this project']);
        }

        $pre_auth;
        try {
            $auth = $pre_auth->refresh();
        } catch (Exception $e) {
            return $view->render($response->withStatus(500), 'pages/auth/select.html', ['centers' => $centers, 'error' => $e->getMessage()]);
        }

        $this->set_cookies($auth, $res_db[0]->result[0]->username);

        $_SESSION['pre_auth'] = null;
        $_SESSION['centers'] = null;
        $_SESSION['auth'] = $auth;

        $routeParser = $app->getRouteCollector()->getRouteParser();
        $home_url = $routeParser->urlFor('home') . '?first=true';

        $response = $app->getResponseFactory()->createResponse();
        return $response->withHeader('Location', $home_url)->withStatus(302);
    }

    public function post_signup(Request $request, Response $response, $args)
    {
        global $app;

        // TODO: $auth = new Auth($_ENV['AUTH_URL']);
        $auth = new Auth('http://auth:9000/auth');

        $view = Twig::fromRequest($request);
        $body = (object) $request->getParsedBody();

        if (empty($body->username) || empty($body->password) || empty($body->conPassword)) {
            return $view->render($response->withStatus(400), 'pages/auth/index.html', ['error' => 'Username and password are required']);
        }

        if ($body->password !== $body->conPassword) {
            return $view->render($response->withStatus(400), 'pages/auth/index.html', ['error' => 'Password confirm does not match']);
        }

        try {
            $auth = $auth->signup($body->username, $body->password);
        } catch (Exception $e) {
            // probably username or password is wrong
            // return response with status 401
            return $view->render($response->withStatus(401), 'pages/auth/index.html', ['error' => $e->getMessage()]);
        }

        $routeParser = $app->getRouteCollector()->getRouteParser();
        $login_url = $routeParser->urlFor('login');

        return $response->withHeader('Location', $login_url)->withStatus(302);
    }

    public function logout(Request $request, Response $response, $args)
    {
        global $app;

        setcookie('user_id', '', time() - 3600, '/');
        setcookie('project', '', time() - 3600, '/');
        setcookie('g_auth', '', time() - 3600, '/');
        setcookie('p_auth', '', time() - 3600, '/');
        setcookie('role', '', time() - 3600, '/');
        setcookie('username', '', time() - 3600, '/');

        $_SESSION['auth'] = null;

        $routeParser = $app->getRouteCollector()->getRouteParser();
        $home_url = $routeParser->urlFor('home');

        $response = $app->getResponseFactory()->createResponse();

        return $response->withHeader('Location', $home_url)->withStatus(302);
    }

    private function set_cookies($auth, $username)
    {
        setcookie('user_id', $auth->user_id, time() + 3600, '/');
        setcookie('project', json_encode($auth->project), time() + 3600, '/');
        setcookie('g_auth', $auth->g_auth, time() + 3600, '/');
        setcookie('p_auth', $auth->p_auth, time() + 3600, '/');
        setcookie('role', $auth->role, time() + 3600, '/');
        setcookie('username', $username, time() + 3600, '/');
    }
}
