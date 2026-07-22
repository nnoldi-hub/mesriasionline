<?php

namespace Tests\Unit;

use App\Models\Video;
use Tests\TestCase;

class VideoModelTest extends TestCase
{
    /**
     * @dataProvider youtubeUrlProvider
     */
    public function test_extracts_youtube_id_from_various_formats(string $input, ?string $expected): void
    {
        $this->assertSame($expected, Video::extractYoutubeId($input));
    }

    public static function youtubeUrlProvider(): array
    {
        return [
            'bare id' => ['dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'watch url' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'watch url with extra params' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=10s', 'dQw4w9WgXcQ'],
            'short url' => ['https://youtu.be/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'shorts url' => ['https://www.youtube.com/shorts/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'embed url' => ['https://www.youtube.com/embed/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'invalid text' => ['nu e un link youtube', null],
            'empty string' => ['', null],
        ];
    }
}
