<?php declare(strict_types=1);

use App\Models\Paragraph;
use PHPUnit\Framework\TestCase;

class ParagraphTest extends TestCase
{
    public function testParagraphConstructor(): void
    {
        $content = [
            ['text' => 'Paragraph 1'],
            ['text' => 'Paragraph 2'],
        ];

        $paragraph = new Paragraph('ref1', 'type1', $content, 'id1');

        $this->assertEquals('id1', $paragraph->id);
        $this->assertEquals('ref1', $paragraph->ref);
        $this->assertEquals('type1', $paragraph->type);
        $this->assertCount(2, $paragraph->content);
        $this->assertIsObject($paragraph->content[0]);
        $this->assertIsObject($paragraph->content[1]);
        $this->assertEquals('Paragraph 1', $paragraph->content[0]->text);
        $this->assertEquals('Paragraph 2', $paragraph->content[1]->text);
    }

    public function testParagraphConstructorWithoutId(): void
    {
        $content = [
            ['text' => 'Paragraph 1'],
            ['text' => 'Paragraph 2'],
        ];

        $paragraph = new Paragraph('ref1', 'type1', $content);

        $this->assertNull($paragraph->id);
        $this->assertEquals('ref1', $paragraph->ref);
        $this->assertEquals('type1', $paragraph->type);
        $this->assertCount(2, $paragraph->content);
        $this->assertIsObject($paragraph->content[0]);
        $this->assertIsObject($paragraph->content[1]);
        $this->assertEquals('Paragraph 1', $paragraph->content[0]->text);
        $this->assertEquals('Paragraph 2', $paragraph->content[1]->text);
    }
}