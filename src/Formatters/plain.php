<?php

namespace Differ\Formatters\Plain;

const COMPLEX_VALUE_STRING_PRESENTATION = 'complex value';

function format(array $ast): string
{
    $format = function (array $data, $keys = []) use (&$format): string {
        $outputLines    = [];
        $parentKeysLine = implode('.', $keys);
    
        foreach ($data as $item) {
            ['key' => $key, 'type' => $type] = $item;
            if ($type === 'nested') {
                $keys[]        = $key;
                $outputLines[] = $format($item['children'], $keys);
                $keys          = [];
                continue;
            }
        
            $key   = $parentKeysLine ? "$parentKeysLine.{$key}" : $key;
            $value = getItemStringPresentation($item['value']);
        
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
                    $newValue      = getItemStringPresentation($item['newValue']);
                    $outputLines[] = "Property '$key' was changed. From '$value' to '$newValue'";
                    break;
            }
        }
    
        return implode("\n", $outputLines);
    };
    
    return $format($ast);
}

function getItemStringPresentation($value): string
{
    return is_array($value) ? COMPLEX_VALUE_STRING_PRESENTATION : $value;
}
