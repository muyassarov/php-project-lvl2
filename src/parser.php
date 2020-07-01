<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parse($rawData, $dataType)
{
    switch ($dataType) {
        case 'json':
            return json_decode($rawData, true);
        case 'yaml':
            return Yaml::parse($rawData);
        default:
            throw new \InvalidArgumentException('Invalid content data type, could not parse raw data');
    }
}
