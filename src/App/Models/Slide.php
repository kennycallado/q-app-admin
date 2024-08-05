<?php declare(strict_types=1);

namespace App\Models;

/**
 * Class slide
 * @package App\Models
 */
class Slide
{
    public ?string $id;
    public string $ref;
    public string $title;
    public array $elements;

    /**
     * @param string $ref
     * @param string $title
     * @param ?string[] $elements
     * @param string|null $id
     */
    public function __construct(string $ref, string $title, ?array $elements = null, ?string $id = null)
    {
        $this->id = $id;
        $this->ref = $ref;
        $this->title = $title;
        $this->elements = $elements ? array_filter($elements) : [];
    }
}
