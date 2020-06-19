<?php

namespace Differ\Formatters\Pretty;

use function Funct\Strings\repeat;

const NUMBER_OF_SPACES = 4;

function format(array $ast, $nestingLevel = 0): string
{
    $outputItems = [];
    $prefix      = getPrefix($nestingLevel);
    foreach ($ast as $datum) {
        ['key' => $key, 'type' => $type] = $datum;

        switch ($type) {
            case 'nested':
                $outputItems[] = "{$prefix}  {$key}: " . format($datum['children'], $nestingLevel + 1);
                break;
            case 'unchanged':
                $value         = toString($datum['value'], $nestingLevel);
                $outputItems[] = "{$prefix}  {$key}: {$value}";
                break;
            case 'changed':
                ['value' => $value, 'newValue' => $newValue] = $datum;
                $value    = toString($value, $nestingLevel);
                $newValue = toString($newValue, $nestingLevel);

                $outputItems[] = "{$prefix}+ {$key}: {$newValue}";
                $outputItems[] = "{$prefix}- {$key}: {$value}";
                break;
            case 'removed':
                $value         = toString($datum['value'], $nestingLevel);
                $outputItems[] = "{$prefix}- {$key}: {$value}";
                break;
            case 'added':
                $value         = toString($datum['value'], $nestingLevel);
                $outputItems[] = "{$prefix}+ {$key}: {$value}";
                break;
        }
    }

    return "{\n  " . implode("\n  ", $outputItems) . "\n{$prefix}}";
}

function convertArrayToString(array $node, int $nestingLevel): string
{
    $prefix = getPrefix($nestingLevel);
    $keys   = array_keys($node);

    $arrayStrings = array_reduce($keys, function ($acc, $key) use ($node, $nestingLevel, $prefix) {
        if (is_array($node[$key])) {
            $acc[] = convertArrayToString($node[$key], ++$nestingLevel);
        } else {
            $acc[] = "$prefix  $key: {$node[$key]}";
        }
        return $acc;
    }, []);

    return "{\n  " . implode("\n  ", $arrayStrings) . "\n{$prefix}}";
}

function convertBoolToString($value): string
{
    return $value ? 'true' : 'false';
}

function toString($value, $nestingLevel): string
{
    if (is_array($value)) {
        return convertArrayToString($value, ++$nestingLevel);
    }
    if (is_bool($value)) {
        return convertBoolToString($value);
    }

    return $value;
}

function getPrefix($nestingLevel)
{
    return repeat(' ', $nestingLevel * NUMBER_OF_SPACES);
}
