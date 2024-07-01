<?php declare(strict_types=1);

namespace App\Models;

class Paper
{
    public ?string $id;
    public string $user;
    public array $answers;
    public bool $completed;
    public string $resource;
    public string $created;

    /**
     * Paper constructor.
     * @param string|null $id
     * @param string $user
     * @param array $answers
     * @param bool $completed
     * @param string $resource
     * @param string $created
     */
    public function __construct(
        ?string $id,
        string $user,
        array $answers,
        bool $completed,
        string $resource,
        string $created
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->answers = $answers;
        $this->completed = $completed;
        $this->resource = $resource;
        $this->created = $created;
    }
}
