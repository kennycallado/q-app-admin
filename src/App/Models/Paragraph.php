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
    /** @var Content[] */
    public array $content;

    /**
     * Paragraph constructor.
     *
     * @param string $ref The reference for the paragraph.
     * @param string $type The type of the paragraph.
     * @param array $content The content of the paragraph.
     * @param string|null $id The unique identifier for the paragraph. Default is null.
     */
    public function __construct(string $ref, string $type, array $content, ?string $id = null)
    {
        if ($id !== null) {
            $this->id = $id;
        }

        $this->ref = $ref;
        $this->type = $type;
        $this->content = $this->arrayToContent($content);
    }

    /**
     * Convert the array to content.
     *
     * @param array $content The content of the paragraph.
     *
     * @return Content[] The content of the paragraph.
     */
    private function arrayToContent(array $content): array
    {
        $result = [];
        foreach ($content as $locale => $value) {
            if (empty($value)) {
                continue;
            }

            $content = new \stdClass();
            $content->locale = $locale;
            $content->text = $value;
            $result[] = $content;
        }

        return $result;
    }
}

/**
 * Class Content
 * @package App\Models
 */
class Content
{
    public string $locale;
    public string $text;
}
