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
}