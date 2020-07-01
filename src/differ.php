<?php

namespace Differ\Differ;

use function Differ\Formatter\format;
use function Differ\Parser\parse;
use function Funct\Collection\union;

function genDiff($firstFilepath, $secondFilepath, $format = 'pretty')
{
    $firstFileDataType    = getFileDataType($firstFilepath);
    $secondFileDataType   = getFileDataType($firstFilepath);
    $firstFileRawContent  = getFileContent($firstFilepath);
    $secondFileRawContent = getFileContent($secondFilepath);
    $firstContent         = parse($firstFileRawContent, $firstFileDataType);
    $secondContent        = parse($secondFileRawContent, $secondFileDataType);

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
    if ($fileExtension == 'yml') {
        return 'yaml';
    }
    return $fileExtension;
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
                return createAstNode($key, 'removed', $oldNode[$key]);
            } elseif (!array_key_exists($key, $oldNode)) {
                return createAstNode($key, 'added', $newNode[$key]);
            } else {
                $value    = $oldNode[$key];
                $newValue = $newNode[$key];
                if (is_array($value) && is_array($newValue)) {
                    $children = $iter($value, $newValue);
                    return createAstNode($key, 'nested', null, null, $children);
                } elseif ($value == $newValue) {
                    return createAstNode($key, 'unchanged', $value);
                } else {
                    return createAstNode($key, 'changed', $value, $newValue);
                }
            }
        }, $unionKeys);
    };

    return $iter($beforeData, $afterData);
}

function createAstNode($key, $type, $value = null, $newValue = null, $children = [])
{
    return [
        'key'      => $key,
        'type'     => $type,
        'value'    => $value,
        'newValue' => $newValue,
        'children' => $children
    ];
}
