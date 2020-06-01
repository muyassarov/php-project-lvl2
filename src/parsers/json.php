<?php

namespace Differ\Parsers\Json;

function parse($filepath)
{
    if (is_file($filepath) && is_readable($filepath)) {
        $content = file_get_contents($filepath);
        return json_decode($content, true);
    }
    throw new \InvalidArgumentException('Invalid file path');
}
