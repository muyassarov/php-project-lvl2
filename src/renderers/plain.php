<?php

namespace Differ\Renderers\Plain;

function render(array $data): string
{
    $outputItems = [];

    foreach ($data as $datum) {
        switch ($datum['type']) {
            case 'unchanged':
                $outputItems[] = "  {$datum['key']}: {$datum['value']}";
                break;
            case 'changed':
                $outputItems[] = "+ {$datum['key']}: {$datum['newValue']}";
                $outputItems[] = "- {$datum['key']}: {$datum['value']}";
                break;
            case 'removed':
                $outputItems[] = "- {$datum['key']}: {$datum['value']}";
                break;
            case 'added':
                $outputItems[] = "+ {$datum['key']}: {$datum['value']}";
                break;
        }
    }

    return "{\n  " . implode("\n  ", $outputItems) . "\n}";
}
