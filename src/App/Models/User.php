<?php declare(strict_types=1);

namespace App\Models;

/**
 * Class User
 * @package App\Models
 */
class User
{
    public string $id;
    public string $username;
    public object $project;
    public ?object $web_token;

    public function __construct(string $id, string $username, object $project, object|string $web_token = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->project = $project instanceof \stdClass ? $project : (object) ['id' => $project];
        $this->web_token = $web_token;
    }

    public static function new_user(string $username, string $password, ?string $project = null): UserNew
    {
        return new UserNew($username, $password, $project);
    }
}
