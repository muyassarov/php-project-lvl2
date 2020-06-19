<?php

namespace Differ\Parsers;

function parse($rawData, $type)
{
    switch ($type) {
        case 'json':
            return \Differ\Parsers\Json\parse($rawData);
        case 'yaml':
            return \Differ\Parsers\Yaml\parse($rawData);
        default:
            throw new \InvalidArgumentException('Invalid content type, could not parse raw data');
    }
}
