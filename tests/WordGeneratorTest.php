<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

use Phonyland\LanguageGenerator\Exceptions\GeneratorException;

class WordGeneratorTest extends BaseTestCase
{
    // region Validations

    /** @test */
    public function first_ngram_length_must_equal_to_n(): void
    {
        // Assert
        $this->expectException(GeneratorException::class);

        // Act
        static::$generator->word(
            lengthHint: 5,
            startsWith: 'aaaaaaaaaa'
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

    // endregion

    /** @test */
    public function it_returns_null_for_a_non_existing_first_ngram(): void
    {
        // Act
        $word = static::$generator->word(
            lengthHint: 5,
            startsWith: 'xxx',
        );

        // Assert
        expect($word)->toBeNull();
    }

    /** @test */
    public function it_can_generate_a_word(): void
    {
        // Act
        $word = static::$generator->word();

        // Assert
        expect($word)->toBeString();
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
    public function length_hint_will_be_a_weighted_random_length_of_the_word_lengths_of_the_model_if_not_set(): void
    {
        // Act
        $word = static::$generator->word();

        // Assert
        expect(mb_strlen($word))->toBeIn(static::$generator->modelData['data']['word_lengths']['i']);
    }

    /** @test */
    public function it_can_generate_a_word_for_a_sentence_position(): void
    {
        // Arrange
        $ngramLenght = static::$generator->modelData['config']['n_gram_size'];
        $position = random_int(1, static::$generator->modelData['config']['number_of_sentence_elements']);
        $position = random_int(0, 1) === 1 ? $position : $position * -1;

        // Act
        $word = static::$generator->word(position: $position);

        // Assert
        $nGramOfWord = mb_substr($word, 0, $ngramLenght);
        expect($nGramOfWord)->toBeIn(static::$generator->modelData['data']['sentence_elements'][$position]['e']);
    }

    /** @test */
    public function it_can_generate_a_word_that_starts_with_a_desired_string(): void
    {
        // Act
        $word = static::$generator->word(startsWith: 'si');

        // Assert
        expect($word)->toStartWith('si');
    }

    /** @test */
    public function it_returns_null_if_there_is_no_words_that_starts_with_the_desired_string(): void
    {
        // Act
        $word = static::$generator->word(startsWith: 'zzz');

        // Assert
        expect($word)->toBeNull();
    }

    /** @test */
    public function it_can_generate_a_word_that_starts_with_a_desired_string_for_a_sentence_position(): void
    {
        $ngramLenght = static::$generator->modelData['config']['n_gram_size'];
        $position = 1;

        // Act
        $word = static::$generator->word(
            position: $position,
            startsWith: 'si',
        );

        // Assert
        $nGramOfWord = mb_substr($word, 0, $ngramLenght);
        expect($nGramOfWord)
            ->toStartWith('si')
            ->toBeIn(static::$generator->modelData['data']['sentence_elements'][$position]['e']);
    }
}
