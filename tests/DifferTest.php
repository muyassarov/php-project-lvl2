<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\diff;
use function Differ\Differ\genDiff;

/**
 * Class DifferTest
 *
 * @package \\${NAMESPACE}
 */
class DifferTest extends TestCase
{

    public function testJson()
    {
        $beforeFilePath = $this->getFixtureFullPath('before.json');
        $afterFilePath = $this->getFixtureFullPath('after.json');
        $expectedJsonFilePath = $this->getFixtureFullPath('expected-json.txt');
        $expectedPrettyFilePath = $this->getFixtureFullPath('expected-pretty.txt');
        $expectedPlainFilePath = $this->getFixtureFullPath('expected-plain.txt');

        $diffJson = genDiff($beforeFilePath, $afterFilePath, 'json');
        $diffPlain = genDiff($beforeFilePath, $afterFilePath, 'plain');
        $diffPretty = genDiff($beforeFilePath, $afterFilePath);

        $this->assertSame(file_get_contents($expectedPlainFilePath), $diffPlain);
        $this->assertSame(file_get_contents($expectedJsonFilePath), $diffJson);
        $this->assertSame(file_get_contents($expectedPrettyFilePath), $diffPretty);
    }

    public function testYaml()
    {
        $beforeFilePath = $this->getFixtureFullPath('before.yaml');
        $afterFilePath  = $this->getFixtureFullPath('after.yaml');
        $expectedPrettyFilePath = $this->getFixtureFullPath('expected-pretty.txt');
        $expectedJsonFilePath = $this->getFixtureFullPath('expected-json.txt');
        $expectedPlainFilePath = $this->getFixtureFullPath('expected-plain.txt');

        $diffPretty = genDiff($beforeFilePath, $afterFilePath);
        $diffJson = genDiff($beforeFilePath, $afterFilePath, 'json');
        $diffPlain = genDiff($beforeFilePath, $afterFilePath, 'plain');

        $this->assertSame(file_get_contents($expectedJsonFilePath), $diffJson);
        $this->assertSame(file_get_contents($expectedPrettyFilePath), $diffPretty);
        $this->assertSame(file_get_contents($expectedPlainFilePath), $diffPlain);
    }

    private function getFixtureFullPath($fixtureName)
    {
        return realpath('tests' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . $fixtureName);
    }
}
