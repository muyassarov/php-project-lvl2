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
    protected $rootPath = '';

    protected function setUp(): void
    {
        $this->rootPath = './tests/fixtures/';
    }

    public function testGenDiffJsonFiles()
    {
        $diff     = genDiff("{$this->rootPath}before.json", "{$this->rootPath}after.json");
        $expected = <<<EOF
{
    host: hexlet.io
  + timeout: 20
  - timeout: 50
  - proxy: 123.234.53.22
  + verbose: 1
}
EOF;

        $this->assertSame($expected, $diff);
    }

    public function testGenDiffYamlFiles()
    {
        $diff     = genDiff("{$this->rootPath}before.yaml", "{$this->rootPath}after.yaml");
        $expected = <<<EOF
{
    host: hexlet.io
  + timeout: 20
  - timeout: 50
  - proxy: 123.234.53.22
  + verbose: 1
}
EOF;
        $this->assertSame($expected, $diff);
    }

    public function testDiff()
    {
        $difference = diff([
            'firstName' => 'Behruz',
            'lastName'  => 'Muyassarov',
            'city'      => 'Dushanbe',
            'phone'     => '+992 987970054',
            'state'     => 'Sino',
        ], [
            'firstName' => 'Behruz',
            'lastName'  => 'Muyassarov',
            'city'      => 'Moscow',
            'phone'     => '+792656572',
            'email'     => 'muyassarov@gmail.com',
        ]);

        $expected = [
            [
                'key'   => 'firstName',
                'type'  => 'unchanged',
                'value' => 'Behruz',
            ],
            [
                'key'   => 'lastName',
                'type'  => 'unchanged',
                'value' => 'Muyassarov',
            ],
            [
                'key'      => 'city',
                'type'     => 'changed',
                'value'    => 'Dushanbe',
                'newValue' => 'Moscow',
            ],
            [
                'key'      => 'phone',
                'type'     => 'changed',
                'value'    => '+992 987970054',
                'newValue' => '+792656572',
            ],
            [
                'key'   => 'state',
                'type'  => 'removed',
                'value' => 'Sino',
            ],
            [
                'key'   => 'email',
                'type'  => 'added',
                'value' => 'muyassarov@gmail.com',
            ],
        ];
        $this->assertEquals($expected, $difference);
    }
}
