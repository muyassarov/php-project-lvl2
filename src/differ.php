<?php

namespace Differ\Differ;

use function Differ\Formatter\format;
use function Differ\Parsers\parse;
use function Funct\Collection\union;

function genDiff($firstFilepath, $secondFilepath, $format = 'pretty')
{
    $extension            = getFileExtension($firstFilepath);
    $firstFileRawContent  = getFileContent($firstFilepath);
    $secondFileRawContent = getFileContent($secondFilepath);
    $firstContent         = parse($firstFileRawContent, $extension);
    $secondContent        = parse($secondFileRawContent, $extension);

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

function getFileExtension(string $filepath): string
{
    return pathinfo($filepath)['extension'];
}

/**
 * Get difference between arrays
 * @param array $beforeData
 * @param array $afterData
 * @return array
 */
function diff(array $beforeData, array $afterData): array
{
    $func = function ($oldNode, $newNode, $acc) use (&$func) {
        $unionKeys = union(array_keys($oldNode), array_keys($newNode));

        return array_reduce($unionKeys, function ($accumulator, $key) use ($oldNode, $newNode, $func) {
            if (!array_key_exists($key, $newNode)) {
                $accumulator[] = [
                    'key'   => $key,
                    'type'  => 'removed',
                    'value' => $oldNode[$key],
                ];
                return $accumulator;
            }
            if (!array_key_exists($key, $oldNode)) {
                $accumulator[] = [
                    'key'   => $key,
                    'type'  => 'added',
                    'value' => $newNode[$key],
                ];
                return $accumulator;
            }

            if (is_array($oldNode[$key]) && is_array($newNode[$key])) {
                $accumulator[] = [
                    'key'      => $key,
                    'type'     => 'nested',
                    'children' => $func($oldNode[$key], $newNode[$key], []),
                ];
            } elseif ($oldNode[$key] == $newNode[$key]) {
                $accumulator[] = [
                    'key'   => $key,
                    'type'  => 'unchanged',
                    'value' => $oldNode[$key],
                ];
            } else {
                $accumulator[] = [
                    'key'      => $key,
                    'type'     => 'changed',
                    'value'    => $oldNode[$key],
                    'newValue' => $newNode[$key],
                ];
            }

            return $accumulator;
        }, $acc);
    };

    return $func($beforeData, $afterData, []);
}
