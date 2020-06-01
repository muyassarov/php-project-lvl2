<?php

namespace Differ\Renderers\Json;

function render(array $data): string
{
    return json_encode($data);
}
