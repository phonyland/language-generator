<?php

declare(strict_types=1);

use Phonyland\LanguageGenerator\Generator;
use Phonyland\LanguageModel\Model;
use Phonyland\NGram\Tokenizer;
use Phonyland\NGram\TokenizerFilter;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    protected static ?Generator $generator = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (static::$generator === null) {
            ray('run');
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
                        ->addWordFilterRule(TokenizerFilter::ALPHABETICAL)
                        ->addSentenceSeparatorPattern(['.', '?', '!', ':', ';', '\n'])
                        ->toLowercase()
                );

            $model->feed(file_get_contents(getcwd().'/tests/stubs/alice.txt'))
                  ->calculate();

            static::$generator = new Generator($model->toArray());
        }
    }

    /** @test */
    public function first_ngram_lenght_must_equal_to_n(): void
    {
        $this->expectException(RuntimeException::class);

        static::$generator->word(
            lengthHint: 5,
            firstNgram: 'aaaaaaaaaa'
        );
    }

    /** @test */
    public function word_position_can_not_be_zero(): void
    {
        $this->expectException(RuntimeException::class);

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
            firstNgram: 'non-existing-ngram'
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
    public function word_firstNgram(): void
    {
        $word = static::$generator->word(
            lengthHint: 5,
            firstNgram: 'the'
        );

        expect($word)->toStartWith('the');
    }

    /** @test */
    public function words(): void
    {
        $words = static::$generator->words(10, 5);

        expect($words)
            ->toBeArray()
            ->toHaveLength(10);
    }

    /** @test */
    public function sentence(): void
    {
        $words = static::$generator->sentence(10);

        expect(explode(' ', $words))->toHaveCount(10);
    }

    /** @test */
    public function sentences(): void
    {
        $sentences = static::$generator->sentences(10);

        expect($sentences)
            ->toBeArray()
            ->toHaveLength(10);
    }

    /** @test */
    public function paragraph(): void
    {
        $paragraph = static::$generator->paragraph(10);

        expect($paragraph)->toBeString();
    }

    /** @test */
    public function paragraphs(): void
    {
        $paragraphs = static::$generator->paragraphs(3, 8);

        expect($paragraphs)
            ->toBeArray()
            ->toHaveLength(3);
    }

    /** @test */
    public function text(): void
    {
        $text = static::$generator->text(200);

        expect($text)->toHaveLength(200);
    }
}
