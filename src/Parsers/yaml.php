<?php

namespace Differ\Parsers\Yaml;

use Symfony\Component\Yaml\Yaml;

function parse($filepath)
{
    return Yaml::parseFile($filepath);
}
