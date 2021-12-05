<?php

namespace Phonyland\LanguageGenerator\Exceptions;

use Exception;
use Phonyland\LanguageGenerator\Generator;

class GeneratorException extends Exception
{
    public static function invalidNGramLength(Generator $generator): self
    {
        return new static(
            message: "First n-Gram lenght must equal to {$generator->modelData['config']['n']} for this model."
        );
    }

    public static function invalidWordPosition(Generator $generator): self
    {
        $numberOfSentenceElements = $generator->modelData['config']['number_of_sentence_elements'];

        return new static(
            message: "Position must be >=-$numberOfSentenceElements or <=+$numberOfSentenceElements and not 0."
        );
    }
}