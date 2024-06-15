<?php declare(strict_types=1);

namespace Core\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class SessionMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $cookie = $request->getCookieParams();

        if (session_status() === PHP_SESSION_NONE) {
            if (isset($cookie['PHPSESSID'])) {
                session_id($cookie['PHPSESSID']);

                session_start();
            } else {
                session_start();
                $request = $request->withCookieParams(['PHPSESSID' => session_id()]);
            }
        }

        return $handler->handle($request);
    }
}
