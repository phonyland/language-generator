<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

use Phonyland\LanguageGenerator\Exceptions\GeneratorException;

class WordGeneratorTest extends BaseTestCase
{
    /** @test */
    public function first_ngram_length_must_equal_to_n(): void
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
    public function position_can_not_be_zero(): void
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
    public function positions_outside_of_the_model_are_not_allowed(): void
    {
        // Assert
        $this->expectException(GeneratorException::class);

        // Act
        static::$generator->word(
            lengthHint: 5,
            position: static::$generator->modelData['config']['number_of_sentence_elements'] + 1
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
    public function word_length_hint_will_a_weighted_random_length_of_the_word_lengths_of_the_model(): void
    {
        // Act
        $word = static::$generator->word();

        // Assert
        expect(mb_strlen($word))
            ->toBeIn(static::$generator->modelData['data']['word_lengths']['i']);
    }

    /** @test */
    public function it_can_generate_a_word_that_starts_with_a_first_n_gram(): void
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
