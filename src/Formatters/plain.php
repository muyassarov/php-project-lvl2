<?php

namespace Differ\Formatters\Plain;

const COMPLEX_VALUE_STRING_PRESENTATION = 'complex value';

function format(array $ast, array $keys = []): string
{
    $outputLines    = [];
    $parentKeysLine = implode('.', $keys);

    foreach ($ast as $item) {
        ['key' => $key, 'type' => $type] = $item;
        if ($type === 'list') {
            $keys[]        = $key;
            $outputLines[] = format($item['children'], $keys);
            $keys          = [];
            continue;
        }

        $key   = $parentKeysLine ? "$parentKeysLine.{$key}" : $key;
        $value = getAstItemValueStringPresentation($item['value']);

        switch ($type) {
            case 'unchanged':
                break;
            case 'removed':
                $outputLines[] = "Property '$key' was removed";
                break;
            case 'added':
                $outputLines[] = "Property '$key' was added with value: '$value'";
                break;
            case 'changed':
                $newValue      = getAstItemValueStringPresentation($item['newValue']);
                $outputLines[] = "Property '$key' was changed. From '$value' to '$newValue'";
                break;
        }
    }

    return implode("\n", $outputLines);
}

function getAstItemValueStringPresentation($value): string
{
    return is_array($value) ? COMPLEX_VALUE_STRING_PRESENTATION : $value;
}
