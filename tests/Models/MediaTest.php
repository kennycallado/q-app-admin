<?php declare(strict_types=1);

use App\Models\Media;
use PHPUnit\Framework\TestCase;

class MediaTest extends TestCase
{
    public function testMediaConstructor(): void
    {
        $media = new Media('ref1', 'url1', 'type1', 'id1', 'alt1');

        $this->assertEquals('id1', $media->id);
        $this->assertEquals('alt1', $media->alt);
        $this->assertEquals('ref1', $media->ref);
        $this->assertEquals('url1', $media->url);
        $this->assertEquals('type1', $media->type);
    }

    public function testMediaConstructorWithoutIdAndAlt(): void
    {
        $media = new Media('ref1', 'url1', 'type1');

        $this->assertNull($media->id);
        $this->assertNull($media->alt);
        $this->assertEquals('ref1', $media->ref);
        $this->assertEquals('url1', $media->url);
        $this->assertEquals('type1', $media->type);
    }
}