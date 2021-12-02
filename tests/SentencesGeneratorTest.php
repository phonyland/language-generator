<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

class SentencesGeneratorTest extends BaseTestCase
{
    /** @test */
    public function it_can_generate_multiple_sentences(): void
    {
        $sentences = static::$generator->sentences(10);

        expect($sentences)
            ->toBeArray()
            ->toHaveLength(10);
    }
}
