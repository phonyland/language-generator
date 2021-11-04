<?php

declare(strict_types=1);

use Phonyland\LanguageGenerator\Generator;
use Phonyland\LanguageModel\Model;
use Phonyland\NGram\TokenizerFilter;

uses()->beforeEach(function () {
    $model = new Model('Test Model');

    $model->config->n(3)
                  ->minLenght(3)
                  ->unique(false)
                  ->excludeOriginals(false)
                  ->frequencyPrecision(7)
                  ->numberOfSentenceElements(5)
                  ->tokenizer
                      ->addWordSeparatorPattern(TokenizerFilter::WHITESPACE_SEPARATOR)
                      ->addWordFilterRule(TokenizerFilter::ALPHABETICAL)
                      ->addSentenceSeparatorPattern(['.', '?', '!', ':', ';', '\n'])
                      ->toLowercase();

    $data = file_get_contents(getcwd().'/tests/stubs/alice.txt');

    $model->feed($data);
    $model->calculate();

    $this->model = $model;
});

it('Generator: word()', function (): void {
    $generator = new Generator($this->model);

    $word = $generator->word(lengthHint: random_int(3, 10));

    expect($word)->toBeString();
});

it('Generator: word(lengthHint)', function (): void {
    $generator = new Generator($this->model);

    $word = $generator->word(lengthHint: 5);

    expect(mb_strlen($word))
        ->toBeGreaterThanOrEqual(3)
        ->toBeLessThanOrEqual(10);
});

it('Generator: word(firstNgram)', function (): void {
    $generator = new Generator($this->model);

    $word = $generator->word(
        lengthHint: 5,
        firstNgram: 'goo'
    );

    expect($word)->toStartWith('goo');
});

it('Generator: wordFromStartOfSentence()', function (): void {
    $generator = new Generator($this->model);

    $word = $generator->wordFromStartOfSentence(
        lengthHint: 5,
        positionFromStart: 1
    );

    expect($word)->toBeString();
});

it('Generator: wordFromEndOfSentence()', function (): void {
    $generator = new Generator($this->model);

    $word = $generator->wordFromEndOfSentence(
        lengthHint: 5,
        positionFromEnd: 1
    );

    expect($word)->toBeString();
});