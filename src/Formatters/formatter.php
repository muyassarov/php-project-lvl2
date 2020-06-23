<?php

namespace Differ\Formatter;

function format($data, $format)
{
    switch ($format) {
        case 'json':
            return json_encode($data);
        case 'plain':
            return \Differ\Formatters\Plain\format($data);
        default:
            return \Differ\Formatters\Pretty\format($data);
    }
}
