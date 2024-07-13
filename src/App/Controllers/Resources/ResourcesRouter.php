<?php declare(strict_types=1);

namespace App\Controllers\Resources;

use Slim\Interfaces\RouteCollectorProxyInterface as Group;

class ResourcesRouter
{
    /**
     * @param Group $group
     */
    public static function routes(Group $group): void
    {
        $group->group('/emias', function (Group $group) {
        });

        $group->group('/slides', function (Group $group) {
            SlidesController::routes($group);
        });

        $group->group('/scripts', function (Group $group) {
        });
    }
}
