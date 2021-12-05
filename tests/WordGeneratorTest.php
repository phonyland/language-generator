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
    public function word_returns_null_for_a_non_existing_first_ngram(): void
    {
        $this->expectException(RuntimeException::class);

        $word = static::$generator->word(
            lengthHint: 5,
            startingNGram: 'non-existing-ngram'
        );

        expect($word)->toBeNull();
    }

    /** @test */
    public function word_with_lengthHint(): void
    {
        $word = static::$generator->word(lengthHint: 3);

        expect(mb_strlen($word))->toBeGreaterThanOrEqual(3);
    }

    /** @test */
    public function word_lengthHint_will_be_the_n_if_not_set(): void
    {
        $word = static::$generator->word();

        expect(mb_strlen($word))->toBeGreaterThanOrEqual(static::$n);
    }

    /** @test */
    public function word_firstNgram(): void
    {
        $word = static::$generator->word(
            lengthHint: 5,
            startingNGram: 'the'
        );

        expect($word)->toStartWith('the');
    }
}
