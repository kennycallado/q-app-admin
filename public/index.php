<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Core\Middleware\SessionMiddleware;
use Core\Render;
use Core\Router;
use Slim\Factory\AppFactory;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\Views\TwigMiddleware;

$app = AppFactory::create();

// Config
$app = Router::routes($app);
$twig = Render::config();  // ?? pass the $app to the config method

// Middlewares
$app->add(SessionMiddleware::class);
$app->add(TwigMiddleware::create($app, $twig));

$app->addRoutingMiddleware();

// Add MethodOverride middleware
$methodOverrideMiddleware = new MethodOverrideMiddleware();
$app->add($methodOverrideMiddleware);

// TODO: better error handling
if (!isset($_ENV['AUTH_URL']) || !isset($_ENV['DATABASE_URL'])) {
    echo '<div style="background-color: #c1c1c1; padding: 2rem; margin: 2rem;border-radius: 1rem;">';
    echo '<h1 style="margin-bottom: 2rem;text-align: center;color: red;"> Environment variables not set</h1>';

    echo '<pre>';
    print_r($_ENV);
    echo '</pre></div>';

    return;
}

if (!isset($_ENV['ENVIRONMENT']) || $_ENV['ENVIRONMENT'] !== 'production') {
    $errorMiddleware = $app->addErrorMiddleware(true, true, true);
} else {
    $errorMiddleware = $app->addErrorMiddleware(false, false, false);
}

// Run
$app->run();
