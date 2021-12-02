<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

class WordsGeneratorTest extends BaseTestCase
{
    /** @test */
    public function it_can_generate_multiple_words(): void
    {
        $words = static::$generator->words(10, 5);

        expect($words)
            ->toBeArray()
            ->toHaveLength(10);
    }
}
