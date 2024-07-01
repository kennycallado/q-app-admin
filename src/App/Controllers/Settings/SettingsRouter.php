<?php declare(strict_types=1);

namespace App\Controllers\Settings;

use Slim\Interfaces\RouteCollectorProxyInterface as Group;

class SettingsRouter
{
    /**
     * @param Group $group
     */
    public static function routes(Group $group): void
    {
        $group->group('/user', function (Group $group) {
            UserSettingsController::routes($group);
        });
    }
}
