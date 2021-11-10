<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator;

use RuntimeException;

class Generator
{
    // region Attributes

    /**
     * @var array<mixed>
     */
    protected array $modelData;

    public int $seed;

    // endregion

    // region Public Methods

    /**
     * @param  array<mixed>     $modelData
     * @param  int|null  $seed
     */
    public function __construct(array $modelData, ?int $seed = null)
    {
        $this->modelData = $modelData;
        $this->seed = $seed ?? mt_rand(0, mt_getrandmax());
        mt_srand($this->seed);
    }

    public function word(
        int $lengthHint,
        ?int $position = null,
        ?string $firstNgram = null,
    ): ?string {
        if ($firstNgram !== null && mb_strlen($firstNgram) !== $this->modelData['config']['n']) {
            throw new RuntimeException("Given first n-Gram lenght must equal to {$this->modelData['config']['n']} for this model.");
        }

        if ($position === 0) {
            throw new RuntimeException('Position can not be zero.');
        }

        if ($firstNgram !== null && ! isset($this->modelData['data']['elements'][$firstNgram])) {
            return null;
        }

        if ($position !== null && $position <= $this->modelData['config']['number_of_sentence_elements']) {
            $ngram = $this->weightedRandom($this->modelData['data']['sentence_elements'][$position]);
        } else {
            $ngram = $firstNgram ?? $this->weightedRandom($this->modelData['data']['first_elements']);
        }

        $ngramElement = $this->modelData['data']['elements'][$ngram];
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

    public function words(
        int $count,
        int $lengthHint,
        ?int $position = null,
        ?string $firstNgram = null,
    ): array {
        $words = [];

        for ($i = 0; $i < $count; $i++) {
            $words[] = $this->word($lengthHint, $position, $firstNgram);
        }

        return $words;
    }

    // endregion

    // region Protected Methods

    /**
     * @param  array<mixed>  $elements
     *
     * @return string
     */
    protected function weightedRandom(array &$elements): string
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
}
