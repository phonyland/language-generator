<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

class WordsGeneratorTest extends BaseTestCase
{
    /** @test */
    public function it_can_generate_multiple_words(): void
    {
        $words = static::$generator->words();

        expect($words)->toBeArray();
    }

    /** @test */
    public function it_can_generate_desired_number_of_words(): void
    {
        $numberOfWords = mt_rand(2, 10);

        $words = static::$generator->words($numberOfWords);

        expect($words)
            ->toBeArray()
            ->toHaveLength($numberOfWords);
    }

    /** @test */
    public function it_can_generate_desired_number_of_words_with_a_length_hint(): void
    {
        $numberOfWords = random_int(2, 10);
        $lengthHint = static::$n;

        $words = static::$generator->words($numberOfWords, $lengthHint);

        expect($words)
            ->toBeArray()
            ->toHaveLength($numberOfWords)
            ->each(fn ($word) => expect(mb_strlen($word->value))->toBeGreaterThanOrEqual($lengthHint));
    }
}
