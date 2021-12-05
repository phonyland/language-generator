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
        $words = static::$generator->sentence(numberOfWords: 10);

        // Assert
        expect(explode(' ', $words))
            ->toHaveCount(10)
            ->each()->toBeString();
    }
}
