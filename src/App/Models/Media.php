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
     * Media constructor.
     *
     * @param string $ref The reference for the media.
     * @param string $url The URL of the media.
     * @param string $type The type of the media.
     * @param string|null $id The unique identifier for the media. Default is null.
     * @param string|null $alt The alternative text for the media. Default is null.
     */
    public function __construct(string $ref, string $url, string $type, ?string $id = null, ?string $alt = null)
    {
        if ($id !== null) {
            $this->id = $id;
        }

        if ($alt !== null) {
            $this->alt = $alt;
        }

        $this->ref = $ref;
        $this->url = $url;
        $this->type = $type;
    }
}
