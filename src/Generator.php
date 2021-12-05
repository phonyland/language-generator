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
    public function weightedRandom(array & $elements): string|int
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
     * @param  int     $numberOfWords
     * @param  string  $endingPunctuation
     *
     * @return string
     *
     * @throws \Phonyland\LanguageGenerator\Exceptions\GeneratorException
     */
    public function sentence(
        ?int $numberOfWords = null,
        string $endingPunctuation = '.',
    ): string {
        $startingWords = [];
        $words = [];
        $endingWords = [];

        if ($numberOfWords === null) {
            $numberOfWords = $this->weightedRandom($this->modelData['data']['sentence_lengths']);
        }

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
     * @param  int     $numberOfSentences
     * @param  string  $endingPunctuation
     *
     * @return array<string>
     *
     * @throws \Phonyland\LanguageGenerator\Exceptions\GeneratorException
     */
    public function sentences(
        ?int $numberOfSentences = null,
        string $endingPunctuation = '.',
    ): array {
        $sentences = [];

        // There is no paragraph lenghts data yet. So we'll just use the sentence length data.
        if ($numberOfSentences === null) {
            $numberOfSentences = $this->weightedRandom($this->modelData['data']['sentence_lengths']);
        }

        for ($i = 0; $i < $numberOfSentences; $i++) {
            $sentences[] = $this->sentence(
                numberOfWords: $this->weightedRandom($this->modelData['data']['sentence_lengths']),
                endingPunctuation: $endingPunctuation,
            );
        }

        return $sentences;
    }

    /**
     * Generates a paragraph.
     *
     * @param  int     $numberOfSentences
     * @param  string  $endingPunctuation
     *
     * @return string
     *
     * @throws \Phonyland\LanguageGenerator\Exceptions\GeneratorException
     */
    public function paragraph(
        int $numberOfSentences = 7,
        string $endingPunctuation = '.',
    ): string {
        return implode(' ', $this->sentences($numberOfSentences, $endingPunctuation));
    }

    /**
     * * Generates multiple paragraphs.
     *
     * @param  int  $numberOfParagraphs
     * @param  int  $numberOfSentences
     *
     * @return array<string>
     *
     * @throws \Phonyland\LanguageGenerator\Exceptions\GeneratorException
     */
    public function paragraphs(
        int $numberOfParagraphs = 3,
        int $numberOfSentences = 7,
    ): array {
        $paragraphs = [];

        for ($i = 0; $i < $numberOfParagraphs; $i++) {
            $paragraphs[] = $this->paragraph($numberOfSentences);
        }

        return $paragraphs;
    }

    /**
     * Generates a text.
     *
     * @param  int     $maxNumberOfCharacters
     * @param  string  $endingPunctuation
     *
     * @return string
     *
     * @throws \Phonyland\LanguageGenerator\Exceptions\GeneratorException
     */
    public function text(
        int $maxNumberOfCharacters,
        string $endingPunctuation = '.',
    ): string {
        $sentences = [];
        $textLength = 0;

        do {
            $sentence = $this->sentence($this->weightedRandom($this->modelData['data']['word_lengths']));
            $textLength += mb_strlen($sentence);
            $sentences[] = $sentence;
        } while ($textLength <= $maxNumberOfCharacters);

        return substr(implode(' ', $sentences), 0, $maxNumberOfCharacters - 1).$endingPunctuation;
    }

    // endregion
}
