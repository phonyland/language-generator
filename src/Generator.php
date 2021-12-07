<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator;

use Phonyland\LanguageGenerator\Exceptions\GeneratorException;

class Generator
{
    // region Attributes

    /** @var array<mixed> */
    public array $modelData;

    public int $seed;

    // endregion

    // region Public Methods

    /**
     * Generator constructor.
     *
     * @param  array<mixed>     $modelData
     * @param  int|null  $seed
     */
    public function __construct(array $modelData, ?int $seed = null)
    {
        $this->modelData = $modelData;
        $this->seed = $seed ?? mt_rand(0, mt_getrandmax());
        mt_srand($this->seed);
    }

    /**
     * @param  array<mixed>  $elements
     *
     * @return string|int
     */
    public function weightedRandom(array &$elements): string|int
    {
        $randomWeight = mt_rand(0, $elements['sw']);

        $low = 0;
        $high = $elements['c'] - 1;

        while ($low <= $high) {
            $probe = (int) (($low + $high) / 2);
            $midValue = $elements['cw'][$probe];

            if ($midValue < $randomWeight) {
                $low = $probe + 1;
            } elseif ($midValue > $randomWeight) {
                $high = $probe - 1;
            } else {
                return $elements['e'][$probe];
            }
        }

        return $elements['e'][$low];
    }

    // endregion

    // region Private Validation Methods

    /**
     * @param  string|null  $startingNGram
     *
     * @throws \Phonyland\LanguageGenerator\Exceptions\GeneratorException
     */
    private function checkStartingNGram(?string $startingNGram = null): void
    {
        if ($startingNGram !== null && mb_strlen($startingNGram) !== $this->modelData['config']['n_gram_size']) {
            throw GeneratorException::invalidStartingNGramLength($this);
        }
    }

    /**
     * @param  int|null  $position
     *
     * @throws \Phonyland\LanguageGenerator\Exceptions\GeneratorException
     */
    private function checkPosition(?int $position = null): void
    {
        if (
            ($position === 0) ||
            ($position !== null && abs($position) > $this->modelData['config']['number_of_sentence_elements'])
        ) {
            throw GeneratorException::invalidWordPosition($this);
        }
    }

    // endregion

    // region Public Generation Methods

    /**
     * Generates a word.
     *
     * @param  int|null     $lengthHint
     * @param  int|null     $position
     * @param  string|null  $startingNGram
     *
     * @return string|null
     *
     * @throws \Phonyland\LanguageGenerator\Exceptions\GeneratorException
     */
    public function word(
        ?int $lengthHint = null,
        ?int $position = null,
        ?string $startingNGram = null,
    ): ?string {
        $this->checkStartingNGram($startingNGram);
        $this->checkPosition($position);

        // Return null if there is no desired starting n-Gram
        if ($startingNGram !== null && ! isset($this->modelData['data']['elements'][$startingNGram])) {
            return null;
        }

        // Set a weighted random length hint from model's word lengths data if not set
        if ($lengthHint === null) {
            $lengthHint = $this->weightedRandom($this->modelData['data']['word_lengths']);
        }

        $ngram = $position !== null
            ? $this->weightedRandom($this->modelData['data']['sentence_elements'][$position])
            : $startingNGram ?? $this->weightedRandom($this->modelData['data']['first_elements']);

        $ngramElement = $this->modelData['data']['elements'][$ngram];
        // Loop until n-gram element's children count OR last children count !== 0
        $loop = $ngramElement['c']['c'] !== 0 || $ngramElement['lc']['c'] !== 0;
        $word = $ngram;

        while ($loop) {
            if (
                ($ngramElement['c']['c'] === 0) ||
                ($lengthHint <= mb_strlen($word) && $ngramElement['lc']['c'] !== 0)
            ) {
                // has any last child?
                if ($ngramElement['lc']['c'] !== 0) {
                    $ngram = $this->weightedRandom($ngramElement['lc']); //random last child
                    $word .= mb_substr($ngram, -1);
                }

                $loop = false; //get out of the loop
            } else {
                $ngram = $this->weightedRandom($ngramElement['c']); //random child
                $word .= mb_substr($ngram, -1);
                $ngramElement = $this->modelData['data']['elements'][$ngram]; //set up the next set of probabilities
            }
        }

        return $word;
    }

    /**
     * Generates multiple words.
     *
     * @param  int|null     $numberOfWords
     * @param  int|null     $lengthHint
     * @param  int|null     $position
     * @param  string|null  $startingNGram
     *
     * @return array<string>
     *
     * @throws \Phonyland\LanguageGenerator\Exceptions\GeneratorException
     */
    public function words(
        ?int $numberOfWords = null,
        ?int $lengthHint = null,
        ?int $position = null,
        ?string $startingNGram = null,
    ): array {
        if ($numberOfWords === null) {
            $numberOfWords = $this->weightedRandom($this->modelData['data']['sentence_lengths']);
        }

        $words = [];
        for ($i = 0; $i < $numberOfWords; $i++) {
            $words[] = $this->word(
                lengthHint: $lengthHint,
                position: $position,
                startingNGram: $startingNGram
            );
        }

        return $words;
    }

    /**
     * Generates a sentence.
     *
     * @param  int|null           $numberOfWords
     * @param  string|array|null  $endingPunctuation
     *
     * @return string
     *
     * @throws \Phonyland\LanguageGenerator\Exceptions\GeneratorException
     */
    public function sentence(
        ?int $numberOfWords = null,
        null|string|array $endingPunctuation = null,
    ): string {
        $startingWords = [];
        $words = [];
        $endingWords = [];

        if ($numberOfWords === null) {
            $numberOfWords = $this->weightedRandom($this->modelData['data']['sentence_lengths']);
        }

        if ($endingPunctuation === null) {
            $endingPunctuation = ['.', '!', '?'];
        }

        $endingPunctuation = is_array($endingPunctuation)
            ? $endingPunctuation[array_rand($endingPunctuation)]
            : $endingPunctuation;

        // This phony language model has any sentence elements?
        if ($this->modelData['config']['number_of_sentence_elements'] > 0) {
            $positionedWordsCount = $numberOfWords >= $this->modelData['config']['number_of_sentence_elements'] * 2
                ? $this->modelData['config']['number_of_sentence_elements']
                : (int) ($numberOfWords / 2);

            for ($i = 0; $i < $positionedWordsCount; $i++) {
                $startingWords[] = $this->word(
                    lengthHint: $this->weightedRandom($this->modelData['data']['word_lengths']),
                    position: $i + 1,
                );

                $endingWords[] = $this->word(
                    lengthHint: $this->weightedRandom($this->modelData['data']['word_lengths']),
                    position: ($i + 1) - ($positionedWordsCount + 1),
                );
            }

            // Set remaining word count
            $numberOfWords -= $positionedWordsCount * 2;
        }

        for ($i = 0; $i < $numberOfWords; $i++) {
            $words[] = $this->word(
                lengthHint: $this->weightedRandom($this->modelData['data']['word_lengths']),
            );
        }

        $words = implode(' ', array_merge($startingWords, $words, $endingWords)).$endingPunctuation;

        return mb_strtoupper(mb_substr($words, 0, 1)).mb_substr($words, 1);
    }

    /**
     * Generates multiple sentences.
     *
     * @param  int|null           $numberOfSentences
     * @param  string|array|null  $endingPunctuation
     *
     * @return array<string>
     *
     * @throws \Phonyland\LanguageGenerator\Exceptions\GeneratorException
     */
    public function sentences(
        ?int $numberOfSentences = null,
        null|string|array $endingPunctuation = null,
    ): array {
        $sentences = [];

        if ($endingPunctuation === null) {
            $endingPunctuation = ['.', '!', '?'];
        }

        // There is no paragraph lenghts data yet on Phony Language Models.
        // So we'll just use the sentence length data.
        if ($numberOfSentences === null) {
            $numberOfSentences = $this->weightedRandom($this->modelData['data']['sentence_lengths']);
        }

        for ($i = 0; $i < $numberOfSentences; $i++) {
            $sentences[] = $this->sentence(
                numberOfWords: $this->weightedRandom($this->modelData['data']['sentence_lengths']),
                endingPunctuation: is_array($endingPunctuation) ? $endingPunctuation[array_rand($endingPunctuation)] : $endingPunctuation,
            );
        }

        return $sentences;
    }

    /**
     * Generates a paragraph.
     *
     * @param  int|null           $numberOfSentences
     * @param  string|array|null  $sentenceEndingPunctuation
     *
     * @return string
     *
     * @throws \Phonyland\LanguageGenerator\Exceptions\GeneratorException
     */
    public function paragraph(
        ?int $numberOfSentences = null,
        null|string|array $sentenceEndingPunctuation = null,
    ): string {
        return implode(' ', $this->sentences($numberOfSentences, $sentenceEndingPunctuation));
    }

    /**
     * * Generates multiple paragraphs.
     *
     * @param  int|null           $numberOfParagraphs
     * @param  int|null           $numberOfSentences
     * @param  string|array|null  $sentenceEndingPunctuation
     *
     * @return array<string>
     *
     * @throws \Phonyland\LanguageGenerator\Exceptions\GeneratorException
     */
    public function paragraphs(
        ?int $numberOfParagraphs = null,
        ?int $numberOfSentences = null,
        null|string|array $sentenceEndingPunctuation = null,
    ): array {
        $paragraphs = [];

        // There is no number of paragraphs data yet on Phony Language Models.
        // So we'll just use the sentence length data.
        if ($numberOfParagraphs === null) {
            $numberOfParagraphs = $this->weightedRandom($this->modelData['data']['sentence_lengths']);
        }

        for ($i = 0; $i < $numberOfParagraphs; $i++) {
            $paragraphs[] = $this->paragraph($numberOfSentences, $sentenceEndingPunctuation);
        }

        return $paragraphs;
    }

    /**
     * Generates a text.
     *
     * @param  int|null  $maxNumberOfCharacters
     * @param  string    $endingPunctuation
     *
     * @return string
     *
     * @throws \Phonyland\LanguageGenerator\Exceptions\GeneratorException
     */
    public function text(
        ?int $maxNumberOfCharacters = null,
        null|string|array $sentenceEndingPunctuation = null,
        ?string $suffix = null,
    ): string {
        $sentences = [];
        $textLength = 0;

        // There is no text lenghts data yet on Phony Language Models.
        // So we'll just use the sentence length data multiplied by 100.
        if ($maxNumberOfCharacters === null) {
            $maxNumberOfCharacters = 100 * $this->weightedRandom($this->modelData['data']['sentence_lengths']);
        }

        do {
            $sentence = $this->sentence(
                numberOfWords: $this->weightedRandom($this->modelData['data']['word_lengths']),
                endingPunctuation: $sentenceEndingPunctuation,
            );
            $textLength += mb_strlen($sentence);
            $sentences[] = $sentence;
        } while ($textLength <= $maxNumberOfCharacters);

        return $suffix === null
            ? substr(implode(' ', $sentences), 0, $maxNumberOfCharacters)
            : substr(implode(' ', $sentences), 0, $maxNumberOfCharacters - mb_strlen($suffix)) . $suffix;
    }
    }

    // endregion
}
