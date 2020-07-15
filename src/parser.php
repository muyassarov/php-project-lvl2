<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parse($rawData, $dataType)
{
    switch ($dataType) {
        case 'json':
            return json_decode($rawData);
        case 'yaml':
            return Yaml::parse($rawData, Yaml::PARSE_OBJECT_FOR_MAP);
        default:
            throw new \InvalidArgumentException('Invalid content data type, could not parse raw data');
    }
}
