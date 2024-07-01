<?php declare(strict_types=1);

namespace Core;

use App\Controllers\Elements\ElementsRouter;
use App\Controllers\Settings\SettingsRouter;
use App\Controllers\AuthController;
use App\Controllers\ExamplesController;
use App\Controllers\IndexController;
use Core\Middleware\AuthMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\App;

class Router
{
    /**
     * @param App $app
     * @return App
     */
    public static function routes(App $app): App
    {
        $app->get('/health', function ($request, Response $response) {
            $response->getBody()->write('OK');

            return $response->withHeader('Content-Type', 'text/plain')->withStatus(200);
        });

        // if already logged in, redirect to home
        $app->group('/auth', function (Group $group) {
            AuthController::routes($group);
        });

        $app->group('/elements', function (Group $group) {
            ElementsRouter::routes($group);
        })->add(AuthMiddleware::class);

        $app->group('/settings', function (Group $group) {
            SettingsRouter::routes($group);
        })->add(AuthMiddleware::class);

        $app->group('/examples', function (Group $group) {
            ExamplesController::routes($group);
        })->add(AuthMiddleware::class);

        $app->group('/', function (Group $group) {
            IndexController::routes($group);
        })->add(AuthMiddleware::class);

        return $app;
    }
}
