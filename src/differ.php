<?php

namespace Differ\Differ;

use function cli\line;

function genDiff($firstFilepath, $secondFilepath, $format = 'pretty')
{
    try {
        $parser            = getParser($firstFilepath);
        $firstFileContent  = $parser($firstFilepath);
        $secondFileContent = $parser($secondFilepath);
    } catch (\Exception $e) {
        line("Error occurred while reading file. Message: {$e->getMessage()}");
        return false;
    }

    $diff = diff($firstFileContent, $secondFileContent);

    try {
        $render = getFormatter($format);
        return $render($diff);
    } catch (\Exception $exception) {
        line("Error occurred while getting formatter. Message: {$exception->getMessage()}");
    }
    return false;
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
        default:
            throw new \Exception('Invalid file type');
    }
}

function getFormatter(string $format): callable
{
    switch ($format) {
        case 'json':
            return function ($data) {
                return \Differ\Formatters\Json\format($data);
            };
        case 'plain':
            return function ($data) {
                return \Differ\Formatters\Plain\format($data);
            };
        default:
            return function ($data) {
                return \Differ\Formatters\Pretty\format($data);
            };
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

    $func = function ($oldNode, $newNode, $acc) use (&$func) {
        foreach ($oldNode as $key => $value) {
            if (!array_key_exists($key, $newNode)) {
                $acc[] = [
                    'key'   => $key,
                    'type'  => 'removed',
                    'value' => $value,
                ];
                continue;
            }

            if (is_array($value) && is_array($newNode[$key])) {
                $acc[] = [
                    'key'      => $key,
                    'type'     => 'list',
                    'children' => $func($value, $newNode[$key], []),
                ];
                continue;
            }

            if ($value == $newNode[$key]) {
                $acc[] = [
                    'key'   => $key,
                    'type'  => 'unchanged',
                    'value' => $value,
                ];
            } else {
                $acc[] = [
                    'key'      => $key,
                    'type'     => 'changed',
                    'value'    => $value,
                    'newValue' => $newNode[$key],
                ];
            }
        }

        foreach ($newNode as $key => $value) {
            if (array_key_exists($key, $oldNode)) {
                continue;
            }
            $acc[] = [
                'key'   => $key,
                'type'  => 'added',
                'value' => $value,
            ];
        }

        return $acc;
    };

    return $func($beforeData, $afterData, []);
}
