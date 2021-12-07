<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

class ParagraphsGeneratorTest extends BaseTestCase
{
    /** @test */
    public function it_can_generate_multiple_paragraphs(): void
    {
        // Act
        $paragraphs = static::$generator->paragraphs();

        // Assert
        expect($paragraphs)
            ->toBeArray()
            ->each()->toBeString();
    }

    /** @test */
    public function it_can_generate_desired_number_of_paragraphs(): void
    {
        // Arrange
        $numberOfParagraphs = random_int(2, 10);

        // Act
        $paragraphs = static::$generator->paragraphs(numberOfParagraphs: $numberOfParagraphs);

        // Assert
        expect($paragraphs)
            ->toBeArray()
            ->toHaveCount($numberOfParagraphs)
            ->each()->toBeString();
    }

    }
}
