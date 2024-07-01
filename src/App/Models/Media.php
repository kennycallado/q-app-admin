<?php declare(strict_types=1);

namespace App\Models;

/**
 * Class Media
 * @package App\Models
 */
class Media
{
    public ?string $id;
    public ?string $alt;
    public string $ref;
    public string $url;
    public string $type;

    /**
     * @param string $ref
     * @param string $url
     * @param string $type
     * @param string|null $id
     * @param string|null $alt
     */
    public function __construct(string $ref, string $url, string $type, ?string $id = null, ?string $alt = null)
    {
        $this->id = $id;
        $this->alt = $alt;
        $this->ref = $ref;
        $this->url = $url;
        $this->type = $type;
    }
}
