<?php

namespace Differ\Formatters\Pretty;

use function Funct\Strings\repeat;

const NUMBER_OF_SPACES = 4;

function render(array $ast): string
{
    $format = function ($data, $depth = 0) use (&$format): string {
        $outputItems = array_map(function ($item) use (&$format, $depth) {
            $prefix = getPrefix($depth);
            ['key' => $key, 'type' => $type] = $item;

            switch ($type) {
                case 'nested':
                    return "{$prefix}  {$key}: " . $format($item['children'], $depth + 1);
                case 'unchanged':
                    $value = stringify($item['value'], $depth);
                    return "{$prefix}  {$key}: {$value}";
                case 'changed':
                    ['value' => $value, 'newValue' => $newValue] = $item;
                    $value    = stringify($value, $depth);
                    $newValue = stringify($newValue, $depth);

                    return "{$prefix}+ {$key}: {$newValue}\n  {$prefix}- {$key}: {$value}";
                    break;
                case 'removed':
                    $value         = stringify($item['value'], $depth);
                    return "{$prefix}- {$key}: {$value}";
                case 'added':
                    $value         = stringify($item['value'], $depth);
                    return "{$prefix}+ {$key}: {$value}";
            }
        }, $data);
        $prefix      = getPrefix($depth);
        return "{\n  " . implode("\n  ", $outputItems) . "\n{$prefix}}";
    };

    return $format($ast);
}

function stringify($value, $depth): string
{
    if (is_array($value)) {
        $prefix = getPrefix($depth);
        $keys   = array_keys($value);

        $arrayStrings = array_reduce($keys, function ($acc, $key) use ($value, $depth, $prefix) {
            if (is_array($value[$key])) {
                $acc[] = stringify($value[$key], $depth + 1);
            } else {
                $acc[] = "$prefix  $key: {$value[$key]}";
            }
            return $acc;
        }, []);

        return "{\n  " . implode("\n  ", $arrayStrings) . "\n{$prefix}}";
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    return $value;
}

function getPrefix($depth)
{
    return repeat(' ', $depth * NUMBER_OF_SPACES);
}
