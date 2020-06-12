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
    private const FIXTURES_PATH = './tests/fixtures/';

    public function testGetDiffJsonComplexFilesJsonOutput()
    {
        $diff     = genDiff(
            self::FIXTURES_PATH . 'before-complex.json',
            self::FIXTURES_PATH . 'after-complex.json',
            'json'
        );
        $expected = file_get_contents(self::FIXTURES_PATH . 'expected-json-complex.txt');
        print_r($expected);
        $this->assertSame($expected, $diff);
    }

    public function testGenDiffJsonComplexFilesPrettyOutput()
    {
        $diff     = genDiff(self::FIXTURES_PATH . 'before-complex.json', self::FIXTURES_PATH . 'after-complex.json');
        $expected = file_get_contents(self::FIXTURES_PATH . 'expected-pretty-complex.txt');

        $this->assertSame($expected, $diff);
    }

    public function testGenDiffJsonComplexFilesPlainOutput()
    {
        $diff     = genDiff(
            self::FIXTURES_PATH . 'before-complex.json',
            self::FIXTURES_PATH . 'after-complex.json',
            'plain'
        );
        $expected = file_get_contents(self::FIXTURES_PATH . 'expected-plain.txt');

        $this->assertSame($expected, $diff);
    }

    public function testGenDiffJsonSimpleFilesPrettyOutput()
    {
        $diff     = genDiff(self::FIXTURES_PATH . 'before.json', self::FIXTURES_PATH . 'after.json');
        $expected = file_get_contents(self::FIXTURES_PATH . 'expected-pretty.txt');

        $this->assertSame($expected, $diff);
    }

    public function testGenDiffYamlSimpleFilesPrettyOutput()
    {
        $diff     = genDiff(self::FIXTURES_PATH . 'before.yaml', self::FIXTURES_PATH . 'after.yaml');
        $expected = file_get_contents(self::FIXTURES_PATH . 'expected-pretty.txt');
        $this->assertSame($expected, $diff);
    }

    public function testDiffAst()
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
