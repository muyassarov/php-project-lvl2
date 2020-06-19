<?php

namespace Differ\Parsers\Json;

function parse($data)
{
    return json_decode($data, true);
}
