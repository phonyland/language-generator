<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

class TextGeneratorTest extends BaseTestCase
{
    /** @test */
    public function it_can_generate_a_text(): void
    {
        // Act
        $text = static::$generator->text();

        // Assert
        expect($text)->toBeString();
    }

    /** @test */
    public function it_can_generate_a_text_with_desired_number_of_characters(): void
    {
        // Arrange
        $numberOfCharacters = random_int(100, 1000);

        // Act
        $text = static::$generator->text(maxNumberOfCharacters: $numberOfCharacters);

        // Assert
        expect($text)->toHaveLength($numberOfCharacters);
    }

    /** @test */
    public function it_can_generate_a_text_with_desired_number_of_characters_and_a_suffix(): void
    {
        // Arrange
        $numberOfCharacters = random_int(100, 1000);

        // Act
        $text = static::$generator->text(
            maxNumberOfCharacters: $numberOfCharacters,
            suffix: '...',
        );

        // Assert
        expect($text)
            ->toHaveLength($numberOfCharacters)
            ->toEndWith('...');
    }
}
