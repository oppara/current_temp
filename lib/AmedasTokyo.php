<?php

namespace Oppara\CurrentTemp;

use Goutte\Client as GoutteClient;

class AmedasTokyo
{
    const FMT_URL = 'http://www.jma.go.jp/jp/amedas_h/%s-44132.html';

    private $hour;

    public function __construct($hour = null)
    {
        $this->client = $this->createClient();
        $this->hour = $this->getHour($hour);
    }

    protected function createClient()
    {
        return new GoutteClient();
    }

    public function getTemperature()
    {
        $url = $this->makeUrl();

        $crawler = $this->client->request('GET', $url);
        $pos = $this->hour + 1;
        $values = $crawler->filter('#tbl_list tr')->eq($pos)->children()->each(function ($node, $i){
            return $node->text();
        });

        return str_replace([' ', ' '], '', $values[1]);
    }

    private function getHour($hour)
    {
        if (is_null($hour) || $hour < 0 || 24 < $hour) {
            $hour = date('G');
        }

        $hour = intval($hour);
        if ($hour === 0) {
            $hour = 24;
        }

        return intval($hour);
    }

    private function makeUrl()
    {
        $state = 'today';
        if ($this->hour == 0 || $this->hour == 24) {
            $state = 'yesterday';
        }

        return sprintf(self::FMT_URL, $state);
    }
}
