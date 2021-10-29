<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@Symfony'                => true,
    'phpdoc_no_empty_return'  => false,
    'array_syntax'            => ['syntax' => 'short'],
    'yoda_style'              => false,
    'binary_operator_spaces'  => [
        'operators' => [
            '=>' => 'align',
            '='  => 'align',
        ],
    ],
    'concat_space'            => ['spacing' => 'one'],
    'not_operator_with_space' => false,
    'increment_style'         => ['style' => 'post'],
];

$finder = Finder::create()
                ->in(__DIR__.DIRECTORY_SEPARATOR.'tests')
                ->in(__DIR__.DIRECTORY_SEPARATOR.'src')
                ->ignoreDotFiles(true)
                ->ignoreVCS(true);

return (new Config())
    ->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
