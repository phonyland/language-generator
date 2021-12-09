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
        $words = static::$generator->words(numberOfWords: $numberOfWords);

        // Assert
        expect($words)
            ->toBeArray()
            ->toHaveLength($numberOfWords);
    }

    /** @test */
    public function it_can_generate_multiple_words_with_a_length_hint(): void
    {
        // Arrange
        $lengthHint = static::$n;

        // Act
        $words = static::$generator->words(lengthHint: $lengthHint);

        // Assert
        expect($words)
            ->toBeArray()
            ->each(fn ($word) => expect(mb_strlen($word->value))->toBeGreaterThanOrEqual($lengthHint));
    }

    /** @test */
    public function it_can_generate_multiple_words_from_desired_word_positions(): void
    {
        // Arrange
        $position = random_int(1, static::$generator->modelData['config']['number_of_sentence_elements']);
        $position = random_int(0, 1) === 1 ? $position : -1 * $position;

        // Act
        $words = static::$generator->words(position: $position);

        // Assert
        expect($words)
            ->toBeArray()
            ->each()->toBeString();
    }

    /** @test */
    public function it_can_generate_multiple_words_starting_with_a_desired_string(): void
    {
        // Act
        $words = static::$generator->words(startsWith: 'al');

        // Assert
        expect($words)
            ->toBeArray()
            ->each()->toStartWith('al');
    }
}
