<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;

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
        $beforeFilePath   = $this->getFixtureFullPath('before.json');
        $afterFilePath    = $this->getFixtureFullPath('after.json');
        $expectedFilePath = $this->getFixtureFullPath('expected-json.txt');
        $diff             = genDiff($beforeFilePath, $afterFilePath, 'json');

        $this->assertSame(file_get_contents($expectedFilePath), $diff);
    }

    public function testPretty()
    {
        $beforeFilePath   = $this->getFixtureFullPath('before.json');
        $afterFilePath    = $this->getFixtureFullPath('after.json');
        $expectedFilePath = $this->getFixtureFullPath('expected-pretty.txt');
        $diff             = genDiff($beforeFilePath, $afterFilePath);

        $this->assertSame(file_get_contents($expectedFilePath), $diff);
    }

    public function testPlain()
    {
        $beforeFilePath   = $this->getFixtureFullPath('before.yaml');
        $afterFilePath    = $this->getFixtureFullPath('after.yaml');
        $expectedFilePath = $this->getFixtureFullPath('expected-plain.txt');
        $diff             = genDiff($beforeFilePath, $afterFilePath, 'plain');

        $this->assertSame(file_get_contents($expectedFilePath), $diff);
    }

    private function getFixtureFullPath($fixtureName)
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode(DIRECTORY_SEPARATOR, $parts));
    }
}
