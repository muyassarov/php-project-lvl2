<?php

use PHPUnit\Framework\TestCase;

use function Differ\Differ\diff;

/**
 * Class DifferTest
 *
 * @package \\${NAMESPACE}
 */
class DifferTest extends TestCase
{

    public function testGenDiff()
    {
        $difference = diff([
            'firstName' => 'Behruz',
            'lastName'  => 'Muyassarov',
            'city'      => 'Dushanbe',
            'phone'     => '+992 987970054',
        ], [
            'firstName' => 'Behruz',
            'lastName'  => 'Muyassarov',
            'city'      => 'Moscow',
            'phone'     => '+792656572',
            'email'     => 'muyassarov@gmail.com',
        ]);

        $expected = <<<EOF
{
    firstName: Behruz
    lastName: Muyassarov
  + city: Moscow
  - city: Dushanbe
  + phone: +792656572
  - phone: +992 987970054
  + email: muyassarov@gmail.com
}
EOF;
        $this->assertEquals($expected, $difference);
    }
}