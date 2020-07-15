<?php

namespace Differ\Formatters\Pretty;

const NUMBER_OF_SPACES = 4;

function render(array $ast): string
{
    $iter = function ($data, $level = 1) use (&$iter): string {
        
        $mapped = array_map(function ($node) use (&$iter, $level) {
            ['key' => $key, 'type' => $type] = $node;
            
            $indent        = str_repeat(' ', NUMBER_OF_SPACES * $level);
            $indentChanged = str_repeat(' ', NUMBER_OF_SPACES * $level - 2);
            $value         = stringify($node['value'], $level);
            $newValue      = stringify($node['newValue'], $level);
            switch ($type) {
                case 'nested':
                    $nodeStringRepresentation = $iter($node['children'], $level + 1);
                    return "{$indent}{$key}: {\n{$nodeStringRepresentation}\n{$indent}}";
                case 'unchanged':
                    return "{$indent}{$key}: {$value}";
                case 'changed':
                    return "{$indentChanged}+ {$key}: {$newValue}\n{$indentChanged}- {$key}: {$value}";
                case 'removed':
                    return "{$indentChanged}- {$key}: {$value}";
                case 'added':
                    return "{$indentChanged}+ {$key}: {$value}";
                default:
                    throw new \Error("Unknown node type: {$type}");
            }
        }, $data);
        return implode("\n", $mapped);
    };
    $stringValue = $iter($ast);
    return "{\n{$stringValue}\n}";
}

function stringify($value, $level)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (!is_object($value) && !is_array($value)) {
        return $value;
    }
    
    $indent     = str_repeat(' ', $level * NUMBER_OF_SPACES + 4);
    $indentLast = str_repeat(' ', $level * NUMBER_OF_SPACES);
    $keys       = array_keys((array)$value);
    
    $mapped = array_map(function ($key) use ($value, $level, $indent) {
        $strValue = stringify($value->{$key}, $level + 1);
        return "{$indent}{$key}: $strValue";
    }, $keys);

    $arrayStringRepresentation = implode("\n", $mapped);
    return "{\n{$arrayStringRepresentation}\n{$indentLast}}";
}
