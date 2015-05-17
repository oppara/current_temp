<?php
/*
 * CurrentTemp (Twitter Bot)
 *
 * https://twitter.com/current_temp
 */

date_default_timezone_set('Asia/Tokyo');

require 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Mailgun\Mailgun;
use Oppara\CurrentTemp\CurrentTemp;


if (!isHeroku()) {
    Dotenv::load(__DIR__);
}

$Logger = new Logger('current_temp');
$Logger->pushHandler(new StreamHandler('php://stderr', Logger::INFO));

set_exception_handler('exception_handler');

if (isWebDyno()) {
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    $Logger->info("Web  browser access. ip:$ip ua:$ua");
    exit(0);
}


$config = [
    'consumer_key'        => getenv('CONSUMER_KEY'),
    'consumer_secret'     => getenv('CONSUMER_SECRET'),
    'access_token'        => getenv('ACCESS_TOKEN'),
    'access_token_secret' => getenv('ACCESS_TOKEN_SECRET'),
    'debug'               => getenv('DEBUG'),
];

$CurrentTemp = new CurrentTemp($config);
$tweet = $CurrentTemp->tweet();
$Logger->info($tweet);


function isHeroku()
{
    return !empty(getenv('DYNO'));
}

function isWebDyno()
{
    return strpos(getenv('DYNO'), 'web') === 0;
}


function exception_handler($exception)
{
    global $Logger;

    $message = $exception->getMessage();
    $Logger->error($message);

    $Mailgun = new Mailgun(getenv('MAILGUN_API_KEY'));
    $domain = getenv('MAILGUN_DOMAIN');
    $Mailgun->sendMessage($domain, [
        'from'    => getenv('MAILGUN_FROM_ADDRESS'),
        'to'      => getenv('MAILGUN_TO_ADDRESS'),
        'subject' => '[ERROR]current_temp',
        'text'    => $message,
    ]);
}
