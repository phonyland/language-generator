<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

class AcrosticPoemGeneratorTest extends BaseTestCase
{
    /** @test */
    public function it_can_generate_an_acrostic_poem(): void
    {
        // Arrange
        $initials = 'emre loves sifa';

        // Act
        $poem = static::$generator->acrosticPoem(initials: $initials);

        // Assert
        $verses = explode(PHP_EOL, $poem);

        foreach ($verses as $index => $verse) {
            if ($verse === '') {
                continue;
            }

            expect(mb_strtolower($verse[0]))->toEqual($initials[$index]);
        }
    }
}
