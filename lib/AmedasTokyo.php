<?php
declare(strict_types=1);

namespace Oppara\CurrentTemp;

class AmedasTokyo
{
    /**
     * 指定した日時の気象情報データの URL
     */
    public const URL_FMT = 'https://www.jma.go.jp/bosai/amedas/data/map/%s.json';

    /**
     * 気象情報データ内の東京の ID
     */
    public const TOKYO = 44132;

    /**
     * __construct
     *
     * @param string $datetimeString Y-m-d H:00:00
     */
    public function __construct($datetimeString = '')
    {
        $this->url = $this->makeUrl($datetimeString);
    }

    public function getTemperature()
    {
        $json = file_get_contents($this->url);
        $data = json_decode($json, true);

        return $data[self::TOKYO]['temp'][0];
    }

    protected function makeUrl($str)
    {
        $datetime = new \DateTime($str, new \DateTimeZone('Asia/Tokyo'));

        return sprintf(self::URL_FMT, $datetime->format('YmdH0000'));
    }
}
