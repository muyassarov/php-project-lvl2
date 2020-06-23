<?php

namespace Differ\Formatters\Pretty;

use function Funct\Strings\repeat;

const NUMBER_OF_SPACES = 4;

function format(array $ast): string
{
    $format = function ($data, $depth = 0) use (&$format): string {
        $outputItems = [];
        $prefix      = getPrefix($depth);
        foreach ($data as $datum) {
            ['key' => $key, 'type' => $type] = $datum;
        
            switch ($type) {
                case 'nested':
                    $outputItems[] = "{$prefix}  {$key}: " . $format($datum['children'], $depth + 1);
                    break;
                case 'unchanged':
                    $value         = toString($datum['value'], $depth);
                    $outputItems[] = "{$prefix}  {$key}: {$value}";
                    break;
                case 'changed':
                    ['value' => $value, 'newValue' => $newValue] = $datum;
                    $value    = toString($value, $depth);
                    $newValue = toString($newValue, $depth);
                
                    $outputItems[] = "{$prefix}+ {$key}: {$newValue}";
                    $outputItems[] = "{$prefix}- {$key}: {$value}";
                    break;
                case 'removed':
                    $value         = toString($datum['value'], $depth);
                    $outputItems[] = "{$prefix}- {$key}: {$value}";
                    break;
                case 'added':
                    $value         = toString($datum['value'], $depth);
                    $outputItems[] = "{$prefix}+ {$key}: {$value}";
                    break;
            }
        }
        
        return "{\n  " . implode("\n  ", $outputItems) . "\n{$prefix}}";
    };
    
    return $format($ast);
}

function convertArrayToString(array $node, int $depth): string
{
    $prefix = getPrefix($depth);
    $keys   = array_keys($node);

    $arrayStrings = array_reduce($keys, function ($acc, $key) use ($node, $depth, $prefix) {
        if (is_array($node[$key])) {
            $acc[] = convertArrayToString($node[$key], ++$depth);
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

function toString($value, $depth): string
{
    if (is_array($value)) {
        return convertArrayToString($value, ++$depth);
    }
    if (is_bool($value)) {
        return convertBoolToString($value);
    }

    return $value;
}

function getPrefix($depth)
{
    return repeat(' ', $depth * NUMBER_OF_SPACES);
}
