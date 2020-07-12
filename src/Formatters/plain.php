<?php

namespace Differ\Formatters\Plain;

use function Funct\Collection\flattenAll;

const COMPLEX_VALUE_STRING_PRESENTATION = 'complex value';

function render(array $ast): string
{
    $iter = function (array $data, $ancestry = '') use (&$iter): string {
        
        $mapped = array_map(function ($node) use (&$iter, $ancestry) {
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
                    return [];
                default:
                    throw new \Error("Unknown node type: {$type}");
            }
        }, $data);

        $outputItems = flattenAll($mapped);
        
        return implode("\n", $outputItems);
    };
    
    return $iter($ast);
}

function stringify($value)
{
    return is_array($value) ? COMPLEX_VALUE_STRING_PRESENTATION : $value;
}
