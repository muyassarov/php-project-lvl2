<?php

namespace Differ\Formatters\Plain;

const COMPLEX_VALUE_STRING_PRESENTATION = 'complex value';

function format(array $ast, $keys = []): string
{
    $outputLines    = [];
    $parentKeysLine = implode('.', $keys);

    foreach ($ast as $item) {
        if ($item['type'] === 'list') {
            $keys[]        = $item['key'];
            $outputLines[] = format($item['children'], $keys);
            $keys          = [];
            continue;
        }

        $key   = $parentKeysLine ? "$parentKeysLine.{$item['key']}" : $item['key'];
        $value = getAstItemValueStringPresentation($item['value']);

        switch ($item['type']) {
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

function getAstItemValueStringPresentation($value)
{
    return is_array($value) ? COMPLEX_VALUE_STRING_PRESENTATION : $value;
}
