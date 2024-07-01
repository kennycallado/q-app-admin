<?php declare(strict_types=1);

namespace App\Models;

/**
 * Class NewUser
 * @package App\Models
 */
class UserNew
{
    public string $username;
    public string $password;
    public ?string $project;  // TODO: Project

    public function __construct(string $username, string $password, ?string $project = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->project = $project;
    }
}
