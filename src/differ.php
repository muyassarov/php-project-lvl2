<?php

namespace Differ\Differ;

use function cli\line;

function genDiff($firstFilepath, $secondFilepath, $format = 'plain')
{
    try {
        $parser            = getParser($firstFilepath);
        $firstFileContent  = $parser($firstFilepath);
        $secondFileContent = $parser($secondFilepath);
    } catch (\Exception $e) {
        line("Error occurred while reading file. Message: {$e->getMessage()}");
        return;
    }

    $diff   = diff($firstFileContent, $secondFileContent);
    $render = getRenderer($format);
    return $render($diff);
}

function getParser(string $filepath): callable
{
    ['extension' => $extension] = pathinfo($filepath);

    switch ($extension) {
        case 'json':
            return function ($filename) {
                return \Differ\Parsers\Json\parse($filename);
            };
        case 'yaml':
            return function ($filename) {
                return \Differ\Parsers\Yaml\parse($filename);
            };
        case 'ini':
            return function ($filename) {
                return \Differ\Parsers\Ini\parse($filename);
            };
        default:
            throw new \Exception('Invalid file type');
    }
}

function getRenderer(string $format): callable
{
    switch ($format) {
        case 'json':
            return function ($data) {
                return \Differ\Renderers\Json\render($data);
            };
        case 'plain':
            return function ($data) {
                return \Differ\Renderers\Plain\render($data);
            };
        default:
            throw new \Exception('Invalid output format');
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
    $diff = [];
    array_walk($beforeData, function ($value, $key) use ($afterData, &$diff) {
        if (array_key_exists($key, $afterData)) {
            if ($value == $afterData[$key]) {
                $diff[] = [
                    'key' => $key,
                    'type' => 'unchanged',
                    'value' => $value
                ];
            } else {
                $diff[] = [
                    'key' => $key,
                    'type' => 'changed',
                    'value' => $value,
                    'newValue' => $afterData[$key],
                ];
            }
        } else {
            $diff[] = [
                'key' => $key,
                'type' => 'removed',
                'value' => $value,
            ];
        }
    });

    array_walk($afterData, function ($item, $key) use ($beforeData, &$diff) {
        if (!array_key_exists($key, $beforeData)) {
            $diff[] = [
                'key' => $key,
                'type' => 'added',
                'value' => $item,
            ];
        }
    });

    return $diff;
}
