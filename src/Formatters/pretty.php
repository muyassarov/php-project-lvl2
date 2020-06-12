<?php

namespace Differ\Formatters\Pretty;

use function Funct\Strings\repeat;

function format(array $ast, $deep = 0): string
{
    $outputItems = [];
    $prefix      = repeat(' ', $deep * 4);
    foreach ($ast as $datum) {
        $key      = $datum['key'];
        $value    = $datum['value'] ?? '';
        $newValue = $datum['newValue'] ?? '';

        if (is_array($value)) {
            $value = convertArrayToString($value, $deep);
        }
        if (is_array($newValue)) {
            $newValue = convertArrayToString($newValue, $deep);
        }
        if (is_bool($value)) {
            $value = convertBoolToString($value);
        }
        if (is_bool($newValue)) {
            $newValue = convertBoolToString($newValue);
        }

        switch ($datum['type']) {
            case 'list':
                $outputItems[] = "{$prefix}  {$key}: " . format($datum['children'], $deep + 1);
                break;
            case 'unchanged':
                $outputItems[] = "{$prefix}  {$key}: {$value}";
                break;
            case 'changed':
                $outputItems[] = "{$prefix}+ {$key}: {$newValue}";
                $outputItems[] = "{$prefix}- {$key}: {$value}";
                break;
            case 'removed':
                $outputItems[] = "{$prefix}- {$key}: {$value}";
                break;
            case 'added':
                $outputItems[] = "{$prefix}+ {$key}: {$value}";
                break;
        }
    }

    return "{\n  " . implode("\n  ", $outputItems) . "\n{$prefix}}";
}

function convertArrayToString($value, $deep)
{
    if (!is_array($value)) {
        return $value;
    }
    $prefix = repeat(' ', ($deep + 1) * 4);
    $values = [];
    foreach ($value as $key => $itemValue) {
        if (is_array($itemValue)) {
            $values[] = convertArrayToString($itemValue, $deep + 1);
        } else {
            $values[] = "$prefix  $key: $itemValue";
        }
    }
    return "{\n  " . implode("\n  ", $values) . "\n{$prefix}}";
}

function convertBoolToString($value)
{
    return $value ? 'true' : 'false';
}
