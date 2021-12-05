<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

use Phonyland\LanguageGenerator\Generator;
use Phonyland\LanguageModel\Model;
use Phonyland\NGram\Tokenizer;
use Phonyland\NGram\TokenizerFilter;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    protected static ?Generator $generator = null;

    protected static int $n = 3;

    protected function setUp(): void
    {
        parent::setUp();

        if (static::$generator === null) {
            $model = new Model('Test Model');
            $model
                ->config
                ->nGramSize(static::$n)
                ->minWordLength(3)
                ->unique(false)
                ->excludeOriginals(true)
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
}
