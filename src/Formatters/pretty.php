<?php

namespace Differ\Formatters\Pretty;

const NUMBER_OF_SPACES = 4;

function render(array $ast): string
{
    $iter = function ($data, $level = 0) use (&$iter): string {
        
        $outputItems = array_map(function ($node) use (&$iter, $level) {
            ['key' => $key, 'type' => $type] = $node;
            
            $indent   = str_repeat(' ', NUMBER_OF_SPACES * $level);
            $value    = stringify($node['value'], $level);
            $newValue = stringify($node['newValue'], $level);
            
            switch ($type) {
                case 'nested':
                    $nodeStringRepresentation = $iter($node['children'], $level + 1);
                    return "{$indent}  {$key}: {$nodeStringRepresentation}";
                case 'unchanged':
                    return "{$indent}  {$key}: {$value}";
                case 'changed':
                    return "{$indent}+ {$key}: {$newValue}\n  {$indent}- {$key}: {$value}";
                case 'removed':
                    return "{$indent}- {$key}: {$value}";
                case 'added':
                    return "{$indent}+ {$key}: {$value}";
                default:
                    throw new \Exception('Invalid node type, node could not be rendered');
            }
        }, $data);
        $indent = str_repeat(' ', NUMBER_OF_SPACES * $level);
        return "{\n  " . implode("\n  ", $outputItems) . "\n{$indent}}";
    };
    return $iter($ast);
}

function stringify($value, $level)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (!is_object($value) && !is_array($value)) {
        return $value;
    }
    
    $indent = str_repeat(' ', ($level + 1) * NUMBER_OF_SPACES);
    $keys   = array_keys($value);
    
    $arrayStrings = array_map(function ($key) use ($value, $level, $indent) {
        if (is_array($value[$key])) {
            $strValue = stringify($value[$key], $level + 1);
            return "$indent  $key: $strValue";
        } else {
            return "$indent  $key: {$value[$key]}";
        }
    }, $keys);
    
    return "{\n  " . implode("\n  ", $arrayStrings) . "\n{$indent}}";
}
