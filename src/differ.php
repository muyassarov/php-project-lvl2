<?php

namespace Differ\Differ;

use function Differ\Formatter\format;
use function Differ\Parsers\parse;
use function Funct\Collection\union;

function genDiff($firstFilepath, $secondFilepath, $format = 'pretty')
{
    $dataType             = getFileDataType($firstFilepath);
    $firstFileRawContent  = getFileContent($firstFilepath);
    $secondFileRawContent = getFileContent($secondFilepath);
    $firstContent         = parse($firstFileRawContent, $dataType);
    $secondContent        = parse($secondFileRawContent, $dataType);
    
    $ast = diff($firstContent, $secondContent);
    return format($ast, $format);
}

function getFileContent(string $filepath): string
{
    if (is_file($filepath) && is_readable($filepath)) {
        return file_get_contents($filepath);
    }
    throw new \Exception("File: {$filepath} could not be read");
}

function getFileDataType(string $filepath): string
{
    $fileExtension = pathinfo($filepath, PATHINFO_EXTENSION);
    switch ($fileExtension) {
        case 'yml':
            return 'yaml';
        default:
            return $fileExtension;
    }
}

/**
 * Get difference between arrays
 * @param array $beforeData
 * @param array $afterData
 * @return array
 */
function diff(array $beforeData, array $afterData): array
{
    $iter = function ($oldNode, $newNode) use (&$iter) {
        $unionKeys = array_values(union(array_keys($oldNode), array_keys($newNode)));
        
        return array_map(function ($key) use ($oldNode, $newNode, $iter) {
            if (!array_key_exists($key, $newNode)) {
                return createAstNode($key, 'removed', ['value' => $oldNode[$key]]);
            }
            if (!array_key_exists($key, $oldNode)) {
                return createAstNode($key, 'added', ['value' => $newNode[$key]]);
            }
            if (is_array($oldNode[$key]) && is_array($newNode[$key])) {
                return createAstNode($key, 'nested', [
                    'children' => $iter($oldNode[$key], $newNode[$key]),
                ]);
            } elseif ($oldNode[$key] == $newNode[$key]) {
                return createAstNode($key, 'unchanged', ['value' => $oldNode[$key]]);
            }
            
            return createAstNode($key, 'changed', [
                'value'    => $oldNode[$key],
                'newValue' => $newNode[$key],
            ]);
        }, $unionKeys);
    };
    
    return $iter($beforeData, $afterData);
}

function createAstNode($key, $type, $extraData = [])
{
    $node = [
        'key'  => $key,
        'type' => $type,
    ];
    return array_merge($node, $extraData);
}
