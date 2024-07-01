<?php declare(strict_types=1);

namespace App\Models;

/**
 * Class Paragraph
 * @package App\Models
 */
class Paragraph
{
    public ?string $id;
    public string $ref;
    public string $type;
    /** @var object[] $content */
    public array $content;

    /**
     * @param string $ref
     * @param string $type
     * @param array $content
     * @param string|null $id
     */
    public function __construct(string $ref, string $type, array $content, ?string $id = null)
    {
        $this->id = $id;
        $this->ref = $ref;
        $this->type = $type;
        $this->content = array_map(function ($c) {
            return (object) $c;
        }, $content);
    }
}
