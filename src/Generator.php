<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator;

use RuntimeException;

class Generator
{
    public array $modelData;
    public int $seed;

    public function __construct(array $model, ?int $seed = null)
    {
        $this->modelData = $model;
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
            $ngram = array_rand($this->modelData['data']['sentence_elements'][$position]);
        } else {
            $ngram = $firstNgram ?? $this->weightedRandom(
                    elements: $this->modelData['data']['first_elements'],
                    count: $this->modelData['data']['first_elements_count'],
                    weightCount: $this->modelData['data']['first_elements_weight_count'],
                );
        }

        $ngramElement = $this->modelData['data']['elements'][$ngram];
        $loop = !empty($ngramElement['c']) || !empty($ngramElement['lc']);
        $word = $ngram;

        while ($loop) {
            if (
                (empty($ngramElement['c'])) ||
                ($lengthHint <= mb_strlen($word) && !empty($ngramElement['lc']))
            ) {
                // has any last child?
                if (!empty($ngramElement['lc'])) {
                    $ngram = array_rand($ngramElement['lc']); //random last child
                    $word .= mb_substr($ngram, -1);
                }

                $loop = false; //get out of the loop
            } else {
                $ngram = array_rand($ngramElement['c']); //random child
                $word .= mb_substr($ngram, -1);
                $ngramElement = $this->modelData['data']['elements'][$ngram]; //set up the next set of probabilities
            }
        }

        return $word;
    }

    public function wordFromStartOfSentence(
        $lengthHint,
        int $positionFromStart,
        ?string $firstNgram = null,
    ): ?string {
        return $this->word(
            lengthHint: $lengthHint,
            position:   $positionFromStart,
            firstNgram: $firstNgram
        );
    }

    public function wordFromEndOfSentence(
        $lengthHint,
        int $positionFromEnd,
        ?string $firstNgram = null,
    ): ?string {
        return $this->word(
            lengthHint: $lengthHint,
            position:   $positionFromEnd * -1,
            firstNgram: $firstNgram
        );
    }

    public function sentence($nbWords = 6, $variableNbWords = true): string
    {
        // exclude previously generated words
    }
}
