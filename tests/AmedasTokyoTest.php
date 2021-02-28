<?php
declare(strict_types=1);

namespace Oppara\CurrentTemp;

use PHPUnit\Framework\TestCase;

class TestAmedasTokyo extends AmedasTokyo
{
    protected function makeUrl($str)
    {
        $ds = DIRECTORY_SEPARATOR;

        return __DIR__ . $ds . 'data' . $ds . '20210228100000.json';
    }
}

class AmedasTokyoTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetTemperature(): void
    {
        $str = '2021-02-28 10:00:00';
        $expected = 7.2;

        $AmedasTokyo = new TestAmedasTokyo($str);
        $actual = $AmedasTokyo->getTemperature();
        $this->assertSame($expected, $actual);
    }
}
