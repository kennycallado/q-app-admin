<?php declare(strict_types=1);

use App\Models\Question;
use App\Models\QuestionContent;
use App\Models\QuestionRange;
use App\Models\QuestionText;
use PHPUnit\Framework\TestCase;

class QuestionTest extends TestCase
{
    public function testQuestionConstructor(): void
    {
        $content = [
            'range' => ['min' => 1, 'max' => 5, 'value' => 3],
            'questions' => [
                ['text' => 'Question 1'],
                ['text' => 'Question 2'],
            ]
        ];

        $question = new Question('ref1', 'range', $content);

        $this->assertEquals('ref1', $question->ref);
        $this->assertEquals('range', $question->type);
        $this->assertInstanceOf(QuestionContent::class, $question->content);
        $this->assertInstanceOf(QuestionRange::class, $question->content->range);
        $this->assertEquals(1, $question->content->range->min);
        $this->assertEquals(5, $question->content->range->max);
        $this->assertEquals(3, $question->content->range->value);
        $this->assertCount(2, $question->content->questions);
    }

    public function testQuestionConstructorWithTextType(): void
    {
        $content = [
            'text' => ['min' => 10, 'max' => 100],
            'questions' => [
                ['text' => 'Question 1'],
                ['text' => 'Question 2'],
            ]
        ];

        $question = new Question('ref2', 'text', $content);

        $this->assertEquals('ref2', $question->ref);
        $this->assertEquals('text', $question->type);
        $this->assertInstanceOf(QuestionContent::class, $question->content);
        $this->assertInstanceOf(QuestionText::class, $question->content->text);
        $this->assertEquals(10, $question->content->text->min);
        $this->assertEquals(100, $question->content->text->max);
        $this->assertCount(2, $question->content->questions);
    }

    public function testArrayToContentWithInvalidType(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown question type');

        $content = [
            'invalid' => [],
            'questions' => [
                ['text' => 'Question 1'],
                ['text' => 'Question 2'],
            ]
        ];

        new Question('ref3', 'invalid', $content);
    }

    public function testQuestionContentConstructorWithInvalidParams(): void
    {
        $this->expectException(TypeError::class);

        $questions = [
            ['text' => 'Question 1'],
            ['text' => 'Question 2'],
        ];

        new QuestionContent(new stdClass(), $questions);
    }

    public function testQuestionRangeInitialization(): void
    {
        $range = new QuestionRange('1', '5', '3');

        $this->assertEquals(1, $range->min);
        $this->assertEquals(5, $range->max);
        $this->assertEquals(3, $range->value);
    }

    public function testQuestionTextInitialization(): void
    {
        $text = new QuestionText('10', '100');

        $this->assertEquals(10, $text->min);
        $this->assertEquals(100, $text->max);
    }
}
