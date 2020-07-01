<?php

namespace Differ\Formatters\Pretty;

use function Funct\Strings\repeat;

const NUMBER_OF_SPACES = 4;

function render(array $ast): string
{
    $iter = function ($data, $depth = 0) use (&$iter): string {
        $prefix      = getPrefix($depth);
        $outputItems = array_map(function ($item) use (&$iter, $depth) {
            $prefix = getPrefix($depth);
            ['key' => $key, 'type' => $type] = $item;
            
            switch ($type) {
                case 'nested':
                    return "{$prefix}  {$key}: " . $iter($item['children'], $depth + 1);
                case 'unchanged':
                    $value = stringify($item['value'], $depth);
                    return "{$prefix}  {$key}: {$value}";
                case 'changed':
                    ['value' => $value, 'newValue' => $newValue] = $item;
                    $value    = stringify($value, $depth);
                    $newValue = stringify($newValue, $depth);
                    return "{$prefix}+ {$key}: {$newValue}\n  {$prefix}- {$key}: {$value}";
                case 'removed':
                    $value = stringify($item['value'], $depth);
                    return "{$prefix}- {$key}: {$value}";
                case 'added':
                    $value = stringify($item['value'], $depth);
                    return "{$prefix}+ {$key}: {$value}";
                default:
                    throw new \Exception('Invalid node type, node could not be rendered');
            }
        }, $data);
        
        return "{\n  " . implode("\n  ", $outputItems) . "\n{$prefix}}";
    };
    
    return $iter($ast);
}

function stringify($value, $depth): string
{
    if (is_array($value)) {
        $prefix = getPrefix($depth + 1);
        $keys   = array_keys($value);
        
        $arrayStrings = array_map(function ($key) use ($value, $depth, $prefix) {
            if (is_array($value[$key])) {
                return "$prefix  $key: " . stringify($value[$key], $depth + 1);
            } else {
                return "$prefix  $key: {$value[$key]}";
            }
        }, $keys);
        
        return "{\n  " . implode("\n  ", $arrayStrings) . "\n{$prefix}}";
    } elseif (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    
    return $value;
}

function getPrefix($depth)
{
    return repeat(' ', $depth * NUMBER_OF_SPACES);
}
