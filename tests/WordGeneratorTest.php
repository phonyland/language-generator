<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

use Phonyland\LanguageGenerator\Exceptions\GeneratorException;

class WordGeneratorTest extends BaseTestCase
{
    /** @test */
    public function first_ngram_lenght_must_equal_to_n(): void
    {
        // Assert
        $this->expectException(GeneratorException::class);

        // Act
        static::$generator->word(
            lengthHint: 5,
            startingNGram: 'aaaaaaaaaa'
        );
    }

    /** @test */
    public function word_position_can_not_be_zero(): void
    {
        // Assert
        $this->expectException(GeneratorException::class);

        // Act
        static::$generator->word(
            lengthHint: 5,
            position: 0
        );
    }

    /** @test */
    public function it_returns_null_for_a_non_existing_first_ngram(): void
    {
        // Act
        $word = static::$generator->word(
            lengthHint: 5,
            startingNGram: 'xxx',
        );

        // Assert
        expect($word)->toBeNull();
    }

    /** @test */
    public function it_can_generate_a_word_with_a_length_hint(): void
    {
        // Act
        $word = static::$generator->word(lengthHint: 3);

        // Assert
        expect(mb_strlen($word))->toBeGreaterThanOrEqual(expected: 3);
    }

    /** @test */
    public function word_length_hint_will_be_the_n_if_not_set(): void
    {
        // Act
        $word = static::$generator->word();

        // Assert
        expect(mb_strlen($word))->toBeGreaterThanOrEqual(expected: static::$n);
    }

    /** @test */
    public function word_first_n_gram(): void
    {
        // Act
        $word = static::$generator->word(
            lengthHint: 5,
            startingNGram: 'the'
        );

        // Assert
        expect($word)->toStartWith('the');
    }
}
