<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

class TextGeneratorTest extends BaseTestCase
{
    /** @test */
    public function it_can_generate_a_text(): void
    {
        $text = static::$generator->text(200);

        expect($text)->toHaveLength(200);
    }
}
