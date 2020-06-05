<?php

namespace Differ\Renderers\Plain;

use function Funct\Strings\repeat;

function render(array $data, $deep = 0): string
{
    $outputItems = [];
    $prefix      = repeat(' ', $deep * 4);
    foreach ($data as $datum) {
        $key      = $datum['key'];
        $value    = $datum['value'] ?? '';
        $newValue = $datum['newValue'] ?? '';

        if (is_array($value)) {
            $value = arrayToString($value, $deep);
        }
        if (is_array($newValue)) {
            $newValue = arrayToString($newValue, $deep);
        }
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
        if (is_bool($newValue)) {
            $newValue = $newValue ? 'true' : 'false';
        }

        switch ($datum['type']) {
            case 'list':
                $outputItems[] = "{$prefix}  {$key}: " . render($datum['children'], $deep + 1);
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

function arrayToString($value, $deep)
{
    $prefix = repeat(' ', ($deep + 1) * 4);
    if (!is_array($value)) {
        return $value;
    }
    $values = [];
    foreach ($value as $key => $itemValue) {
        if (is_array($itemValue)) {
            $values[] = arrayToString($itemValue, $deep + 1);
        } else {
            $values[] = "$prefix  $key: $itemValue";
        }
    }
    return "{\n  " . implode("\n  ", $values) . "\n{$prefix}}";
}
