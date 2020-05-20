<?php

namespace Differ\Differ;

use function cli\line;

function genDiff($firstFilepath, $secondFilepath)
{
    try {
        $firstFileContent = readFile($firstFilepath);
        $secondFileContent = readFile($secondFilepath);
    } catch (\Exception $e) {
        line("File cannot be read. Error: {$e->getMessage()}");
        return;
    }

    $firstJson  = json_decode($firstFileContent, true);
    $secondJson = json_decode($secondFileContent, true);

    return diff($firstJson, $secondJson);
}

function diff(array $beforeData, array $afterData): string
{
    $diff = [];
    array_walk($beforeData, function ($value, $key) use ($afterData, &$diff) {
        if (array_key_exists($key, $afterData)) {
            if ($value == $afterData[$key]) {
                $diff[] = "  $key: $value";
            } else {
                $diff[] = "+ $key: $afterData[$key]";
                $diff[] = "- $key: $value";
            }
        } else {
            $diff[] = "- $key: $value";
        }
    });

    array_walk($afterData, function ($item, $key) use ($beforeData, &$diff) {
        if (!array_key_exists($key, $beforeData)) {
            $diff[] = "+ $key: $item";
        }
    });

    return "{\n  " . implode("\n  ", $diff) . "\n}";
}

function readFile($filepath)
{
    if (is_file($filepath) && is_readable($filepath)) {
        $content = file_get_contents($filepath);
        return $content;
    }
    throw new \InvalidArgumentException('Invalid file path');
}
