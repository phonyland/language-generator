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
}
