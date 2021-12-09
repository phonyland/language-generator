<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

class PoemGeneratorTest extends BaseTestCase
{
    /** @test */
    public function it_can_generate_a_poem(): void
    {
        // Act
        $poem = static::$generator->poem();

        // Assert
        expect($poem)->toBeString();
    }

    /** @test */
    public function it_can_generate_a_poem_with_desired_number_of_verses(): void
    {
        // Arrange
        $numberOfVerses = random_int(10, 20);

        // Act
        $poem = static::$generator->poem(
            numberOfVerses: $numberOfVerses,
            stanzaLength: 0,
        );

        // Assert
        expect(explode(PHP_EOL, $poem))
            ->toHaveCount($numberOfVerses)
            ->each()->toBeString();
    }

    /** @test */
    public function it_can_generate_a_poem_with_desired_stanza_length(): void
    {
        // Arrange
        $numberOfVerses = 120;
        $stanzaLenght = random_int(2, 5);

        // Act
        $poem = static::$generator->poem(
            numberOfVerses: $numberOfVerses,
            stanzaLength: $stanzaLenght,
        );

        expect(explode(PHP_EOL.PHP_EOL, $poem))
            ->toHaveCount($numberOfVerses / $stanzaLenght);
    }
}
