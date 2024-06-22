<?php declare(strict_types=1);

namespace App\Models;

/**
 * Class Question
 * @package App\Models
 */
class Question
{
    public ?string $id;
    public string $ref;
    public string $type;
    public QuestionContent $content;

    /**
     * Question constructor.
     * @param string $ref
     * @param string $type
     * @param QuestionContent $content
     * @param string|null $id
     */
    public function __construct(string $ref, string $type, array $content, ?string $id = null)
    {
        if ($id !== null) {
            $this->id = $id;
        }

        $this->ref = $ref;
        $this->type = $type;
        $this->content = $this->arrayToContent($type, $content);
    }

    /**
     * @param array $content
     * @throws Exception
     * @return array
     */
    private function arrayToContent(string $type, array $content): QuestionContent
    {
        switch ($type) {
            case 'range':
                $params = new QuestionRange(...$content['range']);
                break;
            case 'text':
                $params = new QuestionText(...$content['text']);
                break;
            default:
                throw new \Exception('Unknown question type');
                break;
        };

        $questions = array_map(function ($q) {
            return (object) $q;
        }, $content['questions']);

        return new QuestionContent($params, $questions);
    }
}

class QuestionContent
{
    public ?QuestionRange $range;
    public ?QuestionText $text;
    public array $questions;

    /**
     * QuestionContent constructor.
     * @param QuestionRange|QuestionText $params
     * @param array $questions
     * @throws Exception
     */
    public function __construct(QuestionRange|QuestionText $params, array $questions)
    {
        switch ($params) {
            case $params instanceof QuestionRange:
                $this->range = $params;
                break;
            case $params instanceof QuestionText:
                $this->text = $params;
                break;
            default:
                throw new \Exception('Unknown question type');
                break;
        }

        $this->questions = $questions;
    }
}

/**
 * Class QuestionRange
 * @package App\Models
 */
class QuestionRange
{
    public int $min;
    public int $max;
    public int $value;

    public function __construct(string|int $min, string|int $max, string|int $value)
    {
        $this->min = (int) $min;
        $this->max = (int) $max;
        $this->value = (int) $value;
    }
}

/**
 * Class QuestionText
 * @package App\Models
 */
class QuestionText
{
    public int $min;
    public int $max;

    public function __construct(string|int $min, string|int $max)
    {
        $this->min = (int) $min;
        $this->max = (int) $max;
    }
}
