<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

class ParagraphGeneratorTest extends BaseTestCase
{
    /** @test */
    public function it_can_generate_a_paragraph(): void
    {
        $paragraph = static::$generator->paragraph(10);

        expect($paragraph)->toBeString();
    }
}
