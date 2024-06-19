<?php declare(strict_types=1);

namespace App\Controllers\Elements;

use App\Controllers\Elements\MediaController;
use App\Controllers\Elements\ParagraphsController;
use App\Controllers\Elements\QuestionsController;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

class ElementsRouter
{
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
