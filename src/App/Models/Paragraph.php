<?php declare(strict_types=1);

namespace App\Models;

class Paragraph
{
    public ?string $id;
    public string $ref;
    public string $type;
    /** @var Content[] */
    public array $content;

    public function __construct(?string $id, string $ref, string $type, array $content)
    {
        if ($id !== null) {
            $this->id = $id;
        }

        $this->ref = $ref;
        $this->type = $type;
        $this->content = $this->arrayToContent($content);
    }

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

class Content
{
    public string $locale;
    public string $text;
}
