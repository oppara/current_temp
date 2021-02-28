<?php
declare(strict_types=1);

namespace Oppara\CurrentTemp;

use Goutte\Client as GoutteClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Message\Response as GuzzleResponse;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;
use PHPUnit\Framework\TestCase;

class TestAmedasTokyo extends AmedasTokyo
{
    private $stream;

    protected function createClient()
    {
        $this->setStream();
        $client = new GoutteClient();
        $client->setClient($this->getGuzzle());

        return $client;
    }

    private function setStream(): void
    {
        if ($this->stream === null) {
            $ds = DIRECTORY_SEPARATOR;
            $path = __DIR__ . $ds . 'data' . $ds . '20210228100000.json';
            $resource = fopen($path, 'rb');
            $this->stream = Stream::factory($resource);
        }
    }

    private function getGuzzle()
    {
        $history = new History();
        $mock = new Mock();
        $mock->addResponse(new GuzzleResponse(200, [], $this->stream));
        $guzzle = new GuzzleClient(['redirect.disable' => true, 'base_url' => '']);
        $guzzle->getEmitter()->attach($mock);
        $guzzle->getEmitter()->attach($history);

        return $guzzle;
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
