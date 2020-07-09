<?php

namespace Differ\Formatters\Plain;

const COMPLEX_VALUE_STRING_PRESENTATION = 'complex value';

function render(array $ast): string
{
    $iter = function (array $data, $ancestry = '') use (&$iter): string {
        
        $outputLines = array_map(function ($node) use (&$iter, $ancestry) {
            ['key' => $key, 'type' => $type] = $node;
            $propertyName = "{$ancestry}{$key}";
            $newValue     = stringify($node['newValue']);
            $value        = stringify($node['value']);
            
            switch ($type) {
                case 'nested':
                    return $iter($node['children'], "{$propertyName}.");
                case 'removed':
                    return "Property '{$propertyName}' was removed";
                case 'added':
                    return "Property '{$propertyName}' was added with value: '$value'";
                case 'changed':
                    return "Property '{$propertyName}' was changed. From '$value' to '$newValue'";
                case 'unchanged':
                    break;
            }
        }, $data);
        
        $filteredLines = array_filter($outputLines, function ($item) {
            return !empty($item);
        });
        
        return implode("\n", $filteredLines);
    };
    
    return $iter($ast);
}

function stringify($value)
{
    return is_array($value) ? COMPLEX_VALUE_STRING_PRESENTATION : $value;
}
