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

// should be modified in production
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Run
$app->run();
