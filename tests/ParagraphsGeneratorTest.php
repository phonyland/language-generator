<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

class ParagraphsGeneratorTest extends BaseTestCase
{
    /** @test */
    public function it_can_generate_multiple_paragraphs(): void
    {
        $paragraphs = static::$generator->paragraphs(3, 8);

        expect($paragraphs)
            ->toBeArray()
            ->toHaveLength(3);
    }
}
