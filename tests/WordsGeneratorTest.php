<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

class WordsGeneratorTest extends BaseTestCase
{
    /** @test */
    public function it_can_generate_multiple_words(): void
    {
        $words = static::$generator->words(10, 5);
    /** @test */
    public function it_can_generate_desired_number_of_words(): void
    {
        $numberOfWords = mt_rand(2, 10);

        $words = static::$generator->words($numberOfWords);

        expect($words)
            ->toBeArray()
            ->toHaveLength($numberOfWords);
    }

        expect($words)
            ->toBeArray()
            ->toHaveLength(10);
    }
}
