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
    return view('index');
    //return $app->version();
});

$app->get('/key', function () {
    return str_random(32);
});

$app->get('/exp', 'ExampleController@index');

$app->group(['namespace' => 'App\Http\Controllers\Map', 'prefix' => 'api/map'], function () use ($app) {
    $app->get('nearby-search', 'MapController@nearbySearch');
    $app->get('find-place', 'MapController@findPlace');
    $app->get('place-detail/{placeid}', 'MapController@detailPlace');
});

$app->get('/map-api', 'ExampleController@mapApi');

$app->get('test', function () {
    $xFile = file_get_contents(storage_path() . '/json/my_map.json');

    $response = json_decode($xFile);

    return $response;
    //dd(DB::connection()->getPdo());
});

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