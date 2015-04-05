<?php

namespace Oppara\CurrentTemp;

use Abraham\TwitterOAuth\TwitterOAuth;
use Oppara\CurrentTemp\AmedasTokyo;

class CurrentTemp
{
    const FMT_MESSAGE = '%s°C %s';

    private $dateFormat = 'ga M jS';
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function tweet()
    {
        $temperature = $this->getTemperature();
        if ($temperature == '') {
            throw new \Exception('could not get temperature.');
        }

        $message = $this->makeMessage($temperature);

        $TwitterOAuth = $this->createTwitterOAuth();
        if ($this->config['debug']) {
            $message .= ' : ' . microtime();
        }

        $status = $TwitterOAuth->post('statuses/update', ['status' => $message]);
        if ($TwitterOAuth->getLastHttpCode() != 200) {
            $code = $status->errors[0]->code;
            $message = $status->errors[0]->message;
            throw new \Exception("[$code] $message");
        }

        return $message;
    }

    protected function makeMessage($temperature)
    {
        return sprintf(self::FMT_MESSAGE, $temperature, date($this->dateFormat));
    }

    protected function getTemperature()
    {
        $AmedasTokyo = new \Oppara\CurrentTemp\AmedasTokyo();

        return $AmedasTokyo->getTemperature();
    }

    protected function createTwitterOAuth()
    {
        $cfg = $this->config;

        return new \Abraham\TwitterOAuth\TwitterOAuth($cfg['consumer_key'], $cfg['consumer_secret'], $cfg['access_token'], $cfg['access_token_secret']);
    }


}
