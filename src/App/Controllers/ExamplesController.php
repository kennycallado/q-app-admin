<?php declare(strict_types=1);

namespace App\Controllers;

use App\Models\Repositories\PapersRepository;
use Core\Utils\SurrealDB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

class ExamplesController
{
    public static function routes(Group $group): void
    {
        $group->get('/parti', [self::class, 'parti']);
        $group->get('/dashboard', [self::class, 'dashboard']);
    }

    public function parti(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);
        $auth = $request->getAttribute('auth');

        $i_db = new SurrealDB($auth->project->center, $auth->project->name, $auth->p_auth);
        $g_db = new SurrealDB('global', 'main', $auth->g_auth);

        $papersRepository = new PapersRepository($i_db);

        $papers = $papersRepository->all();
        // $papers = $papersRepository->finByUserId('user', 'users:g10');

        $prepare = [
            'title' => 'Parti',
            'user' => [
                'papers' => $papers
            ]
        ];

        return $view->render($response, 'pages/examples/parti.html', $prepare);
    }

    public function dashboard(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);

        return $view->render($response, 'pages/examples/dashboard.html');
    }
}
