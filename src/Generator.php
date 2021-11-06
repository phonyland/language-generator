<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator;

use Phonyland\LanguageModel\Model;
use RuntimeException;

class Generator
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function word(
        int $lengthHint,
        ?int $position = null,
        ?string $firstNgram = null,
    ): ?string {
        if ($firstNgram !== null && mb_strlen($firstNgram) !== $this->model->config->n) {
            throw new RuntimeException("Given first n-Gram lenght must equal to {$this->model->config->n} for this model.");
        }

        if ($position === 0) {
            throw new RuntimeException('Position can not be zero.');
        }

        if ($firstNgram !== null && ! isset($this->model->elements[$firstNgram])) {
            return null;
        }

        if ($position !== null && $position <= $this->model->config->numberOfSentenceElements) {
            $ngram = array_rand($this->model->sentenceElements[$position]);
        } else {
            $ngram = $firstNgram ?? array_rand($this->model->firstElements);
        }

        $ngramData = $this->model->elements[$ngram];
        $loop = isset($ngramData[0]) || isset($ngramData[1]);
        $word = $ngram;

        while ($loop) {
            if (
                ($ngramData[0] === 0) ||
                ($lengthHint <= mb_strlen($word) && $ngramData[1] !== 0)
            ) {
                // has any last child?
                if ($ngramData[1] !== 0) {
                    $ngram = array_rand($ngramData[1]); //random last child
                    $word .= mb_substr($ngram, -1);
                }

                $loop = false; //get out of the loop
            } else {
                $ngram = array_rand($ngramData[0]); //random child
                $word .= mb_substr($ngram, -1);
                $ngramData = $this->model->elements[$ngram]; //set up the next set of probabilities
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
