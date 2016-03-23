<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('/key', function () {
    return str_random(32);
});

$app->get('/exp', 'ExampleController@index');
$app->get('/map-api', 'ExampleController@mapApi');

$app->get('test', function () {
    dd(DB::connection()->getPdo());
});

//$app->get('/test', function () {
//    return [
//        'success' => true,
//        'result'  => [
//            'message' => 'Test berhasil.'
//        ]
//    ];
//});

$app->get('/elasticsearch', function () {
    $client = \Elasticsearch\ClientBuilder::create()->build();

    $params = [
        'index' => 'my_index',
        'type'  => 'my_type',
        'id'    => 'my_id',
        'body'  => ['testField' => 'abc']
    ];

    $response = $client->index($params);

    return $response;
});

//$app->get('/map-api', function () {
//    /GuzzleHttp\RequestOptions::JSON;
//    $url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=-33.8670,151.1957&radius=500&types=food&name=cruise&key=';
//    $key = 'AIzaSyAp_OxlQ8w6fayEK51b-6mpupMi-wbdOwc';
//    $client = new \GuzzleHttp\Client();
//
//    $request = $client->request('GET', $url . $key);
//    $response = $request->getBody();
//
//    return $response;
//});