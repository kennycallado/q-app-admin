<?php declare(strict_types=1);

namespace App\Controllers\Elements;

use Slim\Interfaces\RouteCollectorProxyInterface as Group;

class ElementsRouter
{
    /**
     * @param Group $group
     */
    public static function routes(Group $group): void
    {
        $group->group('/questions', function (Group $group) {
            QuestionsController::routes($group);
        });

        $group->group('/paragraphs', function (Group $group) {
            ParagraphsController::routes($group);
        });

        $group->group('/media', function (Group $group) {
            MediaController::routes($group);
        });
    }
}
