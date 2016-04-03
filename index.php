<?php

date_default_timezone_set('UTC');
require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();

$app['TWITTER_CONSUMER_KEY'] = '2rPzLUN4s0QEEWocP1H998WeT';
$app['TWITTER_CONSUMER_SECRET'] = 'M1a2jWFWaOve9G9aEvd6RSzV6VarMS1SN8UB5gd4wxi01t01Jq';
$app['TWITTER_ACCESS_TOKEN'] = '235091881-PWyjW7mUyIKGB6WAdq4IrCjEKDhbgmrHtk66Y0V4';
$app['TWITTER_ACCESS_TOKEN_SECRET'] = 'wfC6gcbSA9XnThomHAER8gD1jy1kUVcTD3vGu2WuuMkVR';

$app->get('/', function() use($app) {

    return 'Try	/hello/:name';
});

$app->get('/hello/{name}', function($name) use($app) {

    return 'Hello	' . $app->escape($name);
});

$app->get('/histogram/{username}', function($username) use($app) {

    $twitter_client         = new \Guzzle\Http\Client('https://api.twitter.com/{version}', array(
                  'version' => '1.1'
    ));
    
    $twitter_client->addSubscriber(new \Guzzle\Plugin\Oauth\OauthPlugin(array(
        'consumer_key'      => $app['TWITTER_CONSUMER_KEY'],
        'consumer_secret'   => $app['TWITTER_CONSUMER_SECRET'],
        'token'             => $app['TWITTER_ACCESS_TOKEN'],
        'token_secret'      => $app['TWITTER_ACCESS_TOKEN_SECRET']
    )));

    $request                 = $twitter_client->get('search/tweets.json');
    $request->getQuery()->set('from', $username);
    $request->getQuery()->set('since', date('Y-m-d'));
    $response   = $request->send();
    $tweets     = json_decode($response->getBody(), true);
    $histogram  = array('day' => date('Y-m-d'));
    
    for ($i = 0; $i < 24; $i++) {
        $key             = date('H:00', strtotime(date('Y-m-d') . ' + ' . $i . ' hours')) . '-' . date('H:00', strtotime(date('Y-m-d') . ' + ' . ($i + 1) . ' hours'));
        $histogram[$key] = 0;
    }
    
    foreach ($tweets['statuses'] as $tweet) {
        $key = date('H:00', strtotime($tweet['created_at'])) . '-' . date('H:00', strtotime($tweet['created_at'] . ' + 1 hours'));
        $histogram[$key] ++;
    }

    return $app->json($histogram);
});

$app->run();
