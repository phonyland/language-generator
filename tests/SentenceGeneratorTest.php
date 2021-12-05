<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

class SentenceGeneratorTest extends BaseTestCase
{
    /** @test */
    public function it_can_generate_a_sentence(): void
    {
        // Act
        $sentence = static::$generator->sentence();

        // Assert
        expect(explode(' ', $sentence))
            ->each()->toBeString();
    }

    /** @test */
    public function it_can_generate_a_sentence_with_desired_number_of_words(): void
    {
        // Act
        $sentence = static::$generator->sentence(numberOfWords: 10);

        // Assert
        expect(explode(' ', $sentence))
            ->toHaveCount(10)
            ->each()->toBeString();
    }

    /** @test */
    public function it_can_generate_a_sentence_with_desired_ending_punctuation(): void
    {
        // Act
        $sentence = static::$generator->sentence(endingPunctuation: '?');

        // Assert
        expect($sentence[-1])
            ->toBe('?');
    }
}
