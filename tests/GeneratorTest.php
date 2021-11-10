<?php

declare(strict_types=1);

use Phonyland\LanguageGenerator\Generator;
use Phonyland\LanguageModel\Model;
use Phonyland\NGram\Tokenizer;
use Phonyland\NGram\TokenizerFilter;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    protected static array $modelData = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (isset(static::$modelData)) {
            $model = new Model('Test Model');
            $model->config
                ->n(3)
                ->minLenght(3)
                ->unique(false)
                ->excludeOriginals(true)
                ->frequencyPrecision(7)
                ->numberOfSentenceElements(3)
                ->tokenizer(
                    (new Tokenizer())
                    ->addWordSeparatorPattern(TokenizerFilter::WHITESPACE_SEPARATOR)
                    ->addWordFilterRule(TokenizerFilter::LATIN_EXTENDED_ALPHABETICAL)
                    ->addSentenceSeparatorPattern(['.', '?', '!', ':', ';', '\n'])
                    ->toLowercase()
                );

            static::$modelData = $model
                ->feed(file_get_contents(getcwd().'/tests/stubs/alice.txt'))
                ->calculate()
                ->toArray();
        }
    }

    /** @test */
    public function first_ngram_lenght_must_equal_to_n(): void
    {
        $generator = new Generator(static::$modelData);

        $this->expectException(RuntimeException::class);

        $generator->word(
            lengthHint: 5,
            firstNgram: 'aaaaaaaaaa'
        );
    }

    /** @test */
    public function word_position_can_not_be_zero(): void
    {
        $generator = new Generator(static::$modelData);

        $this->expectException(RuntimeException::class);

        $generator->word(
            lengthHint: 5,
            position: 0
        );
    }

    /** @test */
    public function word_returns_null_for_a_non_existing_first_ngram(): void
    {
        $generator = new Generator(static::$modelData);

        $this->expectException(RuntimeException::class);

        $word = $generator->word(
            lengthHint: 5,
            firstNgram: 'non-existing-ngram'
        );

        expect($word)->toBeNull();
    }

    /** @test */
    public function word_with_lengthHint(): void
    {
        $generator = new Generator(static::$modelData);

        $word = $generator->word(lengthHint: 3);

        expect(mb_strlen($word))->toBeGreaterThanOrEqual(3);
    }

    /** @test */
    public function word_firstNgram(): void
    {
        $generator = new Generator(static::$modelData);

        $word = $generator->word(
            lengthHint: 5,
            firstNgram: 'the'
        );

        expect($word)->toStartWith('the');
    }

    /** @test */
    public function wordFromStartOfSentence(): void
    {
        $generator = new Generator(static::$modelData);

        $word = $generator->wordFromStartOfSentence(
            lengthHint: 5,
            positionFromStart: 1
        );

        expect($word)->toBeString();
    }

    /** @test */
    public function wordFromEndOfSentence(): void
    {
        $generator = new Generator(static::$modelData);

        $word = $generator->wordFromEndOfSentence(
            lengthHint: 5,
            positionFromEnd: 1
        );

        expect($word)->toBeString();
    }
}
