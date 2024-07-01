<?php declare(strict_types=1);

namespace Core\Middleware;

use Core\Utils\Auth;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware implements Middleware
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        global $app;

        $cookie = $request->getCookieParams();
        $loggedInTest = false;

        // check if auth is in session
        if (isset($_SESSION['auth']) && !empty($_SESSION['auth'])) {
            /** @var Auth $auth */
            $auth = $_SESSION['auth'];

            $loggedInTest = true;
        } elseif (
            isset($cookie['username']) && !empty($cookie['username']) ||
            isset($cookie['user_id']) && !empty($cookie['user_id']) ||
            isset($cookie['project']) && !empty($cookie['project']) ||
            isset($cookie['g_auth']) && !empty($cookie['g_auth']) ||
            isset($cookie['p_auth']) && !empty($cookie['p_auth']) ||
            isset($cookie['role']) && !empty($cookie['role'])
        ) {
            // TODO: $auth = new Auth($_ENV['AUTH_URL']);
            $auth = new Auth('http://auth:9000/auth');

            $auth->project = json_decode($cookie['project']);
            $auth->username = $cookie['username'];
            $auth->user_id = $cookie['user_id'];
            $auth->g_auth = $cookie['g_auth'];
            $auth->p_auth = $cookie['p_auth'];
            $auth->role = $cookie['role'];

            $loggedInTest = true;
        }

        if ($loggedInTest) {
            $response = $handler->handle($request->withAttribute('auth', $auth));

            return $response;
        } else {
            $routeParser = $app->getRouteCollector()->getRouteParser();
            $response = $app->getResponseFactory()->createResponse();
            $login_url = $routeParser->urlFor('login');

            return $response->withHeader('Location', $login_url)->withStatus(302);
        }
    }
}
