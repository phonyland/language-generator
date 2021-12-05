<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

class WordsGeneratorTest extends BaseTestCase
{
    /** @test */
    public function it_can_generate_multiple_words(): void
    {
        // Act
        $words = static::$generator->words();

        // Assert
        expect($words)->toBeArray();
    }

    /** @test */
    public function it_can_generate_desired_number_of_words(): void
    {
        // Arrange
        $numberOfWords = random_int(2, 10);

        // Act
        $words = static::$generator->words($numberOfWords);

        // Assert
        expect($words)
            ->toBeArray()
            ->toHaveLength($numberOfWords);
    }

    /** @test */
    public function it_can_generate_multiple_words_with_a_length_hint(): void
    {
        // Act
        $words = static::$generator->words(lengthHint: static::$n);

        // Assert
        expect($words)
            ->toBeArray()
            ->each(fn ($word) => expect(mb_strlen($word->value))->toBeGreaterThanOrEqual(static::$n));
    }

    /** @test */
    public function it_can_generate_desired_number_of_words_with_a_length_hint(): void
    {
        // Arrange
        $numberOfWords = random_int(2, 10);
        $lengthHint = static::$n;

        // Act
        $words = static::$generator->words($numberOfWords, $lengthHint);

        // Assert
        expect($words)
            ->toBeArray()
            ->toHaveLength($numberOfWords)
            ->each(fn ($word) => expect(mb_strlen($word->value))->toBeGreaterThanOrEqual($lengthHint));
    }
}
