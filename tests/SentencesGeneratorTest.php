<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

class SentencesGeneratorTest extends BaseTestCase
{
    /** @test */
    public function it_can_generate_multiple_sentences(): void
    {
        // Act
        $sentences = static::$generator->sentences();

        // Assert
        expect($sentences)
            ->toBeArray()
            ->each()->toBeString();
    }

    /** @test */
    public function it_can_generate_desired_number_of_sentences(): void
    {
        // Act
        $sentences = static::$generator->sentences(numberOfSentences: 10);

        // Assert
        expect($sentences)
            ->toBeArray()
            ->toHaveLength(10);
    }

    /** @test */
    public function it_can_generate_multiple_sentences_with_array_of_ending_punctuations(): void
    {
        // Arrange
        $endingPunctuations = ['#', '@', '='];
        // Act
        $sentences = static::$generator->sentences(endingPunctuation: $endingPunctuations);

        // Assert
        expect($sentences)
            ->toBeArray()
            ->each(fn ($word) => expect($word->value[-1])->toBeIn($endingPunctuations));
    }
}
