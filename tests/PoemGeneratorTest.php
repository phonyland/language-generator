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
}
