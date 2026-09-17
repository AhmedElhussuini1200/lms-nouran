<?php

namespace Tests\Unit;

use Tests\TestCase;

class HelpersTest extends TestCase
{
    public function test_youtube_id_extraction(): void
    {
        $this->assertSame('dQw4w9WgXcQ', youtubeId('https://www.youtube.com/watch?v=dQw4w9WgXcQ'));
        $this->assertSame('dQw4w9WgXcQ', youtubeId('https://youtu.be/dQw4w9WgXcQ'));
        $this->assertSame('dQw4w9WgXcQ', youtubeId('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0'));
        $this->assertSame('dQw4w9WgXcQ', youtubeId('https://www.youtube.com/shorts/dQw4w9WgXcQ'));
        $this->assertNull(youtubeId('https://www.youtube.com/embed/xyz123?rel=0'));
        $this->assertNull(youtubeId('not-a-url'));
        $this->assertNull(youtubeId(null));
    }

    public function test_youtube_embed_builder(): void
    {
        $this->assertSame(
            'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0',
            youtubeEmbed('https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=10')
        );
        $this->assertNull(youtubeEmbed('https://example.com/x'));
    }

    public function test_otp_hash_roundtrip(): void
    {
        $code = '123456';
        $hash = hash('sha256', $code);
        $this->assertTrue(hash_equals($hash, hash('sha256', '123456')));
        $this->assertFalse(hash_equals($hash, hash('sha256', '000000')));
    }
}
