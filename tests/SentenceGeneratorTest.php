<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

class SentenceGeneratorTest extends BaseTestCase
{
    /** @test */
    public function it_can_generate_a_sentence(): void
    {
        $words = static::$generator->sentence(10);

        expect(explode(' ', $words))->toHaveCount(10);
    }
}
