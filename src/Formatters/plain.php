<?php

namespace Differ\Formatters\Plain;

const COMPLEX_VALUE_STRING_PRESENTATION = 'complex value';

function render(array $ast): string
{
    $format = function (array $data, $keys = []) use (&$format): string {

        $outputLines = array_reduce($data, function ($acc, $item) use (&$format, $keys) {
            $parentKeysLine = implode('.', $keys);
            ['key' => $key, 'type' => $type] = $item;

            if ($type === 'nested') {
                $keys[] = $key;
                $acc[]  = $format($item['children'], $keys);
                return $acc;
            }

            $key   = $parentKeysLine ? "$parentKeysLine.{$key}" : $key;
            $value = stringify($item['value']);

            switch ($type) {
                case 'removed':
                    $acc[] = "Property '$key' was removed";
                    break;
                case 'added':
                    $acc[] = "Property '$key' was added with value: '$value'";
                    break;
                case 'changed':
                    $newValue = stringify($item['newValue']);
                    $acc[]    = "Property '$key' was changed. From '$value' to '$newValue'";
                    break;
            }
            return $acc;
        }, []);

        return implode("\n", $outputLines);
    };

    return $format($ast);
}

function stringify($value): string
{
    return is_array($value) ? COMPLEX_VALUE_STRING_PRESENTATION : $value;
}
