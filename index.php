<?php

date_default_timezone_set('UTC');
require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();

$app['TWITTER_CONSUMER_KEY'] = '';
$app['TWITTER_CONSUMER_SECRET'] = '';
$app['TWITTER_ACCESS_TOKEN'] = '';
$app['TWITTER_ACCESS_TOKEN_SECRET'] = '';

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
    $histogram  = array();
    
    for ($i = 0; $i < 24; $i++) {
        $key             = date('H:00', strtotime(date('Y-m-d') . ' + ' . $i . ' hours')) . '-' . date('H:00', strtotime(date('Y-m-d') . ' + ' . ($i + 1) . ' hours'));
        $histogram[$key] = 0;
    }
    
    foreach ($tweets['statuses'] as $tweet) {
        $key = date('H:00', strtotime($tweet['created_at'])) . '-' . date('H:00', strtotime($tweet['created_at'] . ' + 1 hours'));
        $histogram[$key] ++;
    }
    
    $maxs                     = array_keys($histogram, max($histogram));
    $histogram['most_active'] = ($maxs[0] != 0 )? implode(',', $maxs) : 'no tweet or invalid user';
    $histogram['day']         = date('Y-m-d');

    return $app->json($histogram);
});

$app->run();
