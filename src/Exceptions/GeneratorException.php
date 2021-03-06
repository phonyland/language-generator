<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Exceptions;

use Exception;
use Phonyland\LanguageGenerator\Generator;

final class GeneratorException extends Exception
{
    /**
     * @param  \Phonyland\LanguageGenerator\Generator  $generator
     *
     * @return static
     */
    public static function invalidStartString(Generator $generator): self
    {
        return new static(
            message: "First n-Gram length must be less than and equal to {$generator->modelData['config']['n_gram_size']} for this model."
        );
    }

    /**
     * @param  \Phonyland\LanguageGenerator\Generator  $generator
     *
     * @return static
     */
    public static function invalidWordPosition(Generator $generator): self
    {
        $numberOfSentenceElements = $generator->modelData['config']['number_of_sentence_elements'];

        return new static(
            message: "Position must be >=-$numberOfSentenceElements or <=+$numberOfSentenceElements and not 0."
        );
    }
}
