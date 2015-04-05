<?php

namespace Oppara\CurrentTemp;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Message\Response as GuzzleResponse;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;
use Oppara\CurrentTemp\AmedasTokyo;


class TestAmedasTokyo extends \Oppara\CurrentTemp\AmedasTokyo
{
    private $stream;

    protected function createClient()
    {
        $this->setStream();
        $client = new \Goutte\Client();
        $client->setClient($this->getGuzzle());

        return $client;
    }

    private function setStream()
    {
        if (is_null($this->stream)) {
            $ds = DIRECTORY_SEPARATOR;
            $path = __DIR__ . $ds . 'data' . $ds . '20150404.html';
            $resource = fopen($path, 'r');
            $this->stream = Stream::factory($resource);
        }
    }

    private function getGuzzle()
    {
        $history = new History();
        $mock = new Mock();
        $mock->addResponse(new GuzzleResponse(200, array(), $this->stream));
        $guzzle = new GuzzleClient(array('redirect.disable' => true, 'base_url' => ''));
        $guzzle->getEmitter()->attach($mock);
        $guzzle->getEmitter()->attach($history);

        return $guzzle;
    }

}

class AmedasTokyoTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     * @dataProvider temperatureProvider
     */
    public function getTemperature($hour, $expected)
    {
        $AmedasTokyo = new TestAmedasTokyo($hour);
        $actual = $AmedasTokyo->getTemperature();
        $this->assertSame($expected, $actual);
    }

    public function temperatureProvider()
    {
    return array(
        // hour, temperature
        array(0, '10.1'),
        array(1, '18.6'),
        array(2, '17.2'),
        array(3, '16.7'),
        array(4, '18.0'),
        array(5, '17.5'),
        array(6, '15.7'),
        array(7, '12.9'),
        array(8, '12.6'),
        array(9, '12.2'),
        array(10, '11.5'),
        array(11, '12.4'),
        array(12, '12.4'),
        array(13, '12.3'),
        array(14, '13.2'),
        array(15, '13.2'),
        array(16, '12.6'),
        array(17, '12.5'),
        array(18, '11.3'),
        array(19, '11.1'),
        array(20, '10.7'),
        array(21, '10.6'),
        array(22, '10.3'),
        array(23, '10.1'),
        array(24, '10.1'),
    );


    }

}

