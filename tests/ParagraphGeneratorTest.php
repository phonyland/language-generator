<?php

declare(strict_types=1);

namespace Phonyland\LanguageGenerator\Tests;

class ParagraphGeneratorTest extends BaseTestCase
{
    /** @test */
    public function it_can_generate_a_paragraph(): void
    {
        // Act
        $paragraph = static::$generator->paragraph();

        // Assert
        expect($paragraph)->toBeString();
    }

    /** @test */
    public function it_can_generate_a_paragraph_with_desired_number_of_sentences_and_sentence_ending_punctuations(): void
    {
        // Act
        $paragraph = static::$generator->paragraph(
            numberOfSentences: 10,
            sentenceEndingPunctuation: '#',
        );

        // Assert
        expect(explode('# ', $paragraph))->toHaveCount(10);
    }
}
