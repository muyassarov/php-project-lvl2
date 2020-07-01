<?php

namespace Differ\Formatter;

function format($data, $format)
{
    switch ($format) {
        case 'json':
            return json_encode($data);
        case 'plain':
            return \Differ\Formatters\Plain\render($data);
        case 'pretty':
            return \Differ\Formatters\Pretty\render($data);
        default:
            throw new \InvalidArgumentException('Invalid format, could not format output data');
    }
}
