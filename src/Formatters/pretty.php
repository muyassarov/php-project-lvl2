<?php

namespace Differ\Formatters\Pretty;

use function Funct\Strings\repeat;

const NUMBER_OF_SPACES = 4;

function format(array $ast, $level = 0): string
{
    $outputItems = [];
    $prefix      = repeat(' ', $level * NUMBER_OF_SPACES);
    foreach ($ast as $datum) {
        ['key' => $key, 'type' => $type, 'value' => $value, 'newValue' => $newValue] = $datum;

        switch ($type) {
            case 'list':
                $outputItems[] = "{$prefix}  {$key}: " . format($datum['children'], $level + 1);
                break;
            case 'unchanged':
                $value = toString($value, $level);
                $outputItems[] = "{$prefix}  {$key}: {$value}";
                break;
            case 'changed':
                $value    = toString($value, $level);
                $newValue = toString($newValue, $level);

                $outputItems[] = "{$prefix}+ {$key}: {$newValue}";
                $outputItems[] = "{$prefix}- {$key}: {$value}";
                break;
            case 'removed':
                $value = toString($value, $level);
                $outputItems[] = "{$prefix}- {$key}: {$value}";
                break;
            case 'added':
                $value = toString($value, $level);
                $outputItems[] = "{$prefix}+ {$key}: {$value}";
                break;
        }
    }

    return "{\n  " . implode("\n  ", $outputItems) . "\n{$prefix}}";
}

function convertArrayToString(array $value, int $level): string
{
    $prefix = repeat(' ', ($level + 1) * NUMBER_OF_SPACES);
    $values = [];
    foreach ($value as $key => $itemValue) {
        if (is_array($itemValue)) {
            $values[] = convertArrayToString($itemValue, $level + 1);
        } else {
            $values[] = "$prefix  $key: $itemValue";
        }
    }
    return "{\n  " . implode("\n  ", $values) . "\n{$prefix}}";
}

function convertBoolToString($value): string
{
    return $value ? 'true' : 'false';
}

function toString($value, $deep): string
{
    if (is_array($value)) {
        return convertArrayToString($value, $deep);
    }
    if (is_bool($value)) {
        return convertBoolToString($value);
    }

    return $value;
}
