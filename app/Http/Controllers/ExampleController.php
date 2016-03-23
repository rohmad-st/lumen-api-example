<?php

namespace App\Http\Controllers;

use Elasticsearch\ClientBuilder;
use GuzzleHttp\Client;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
//        $client = ClientBuilder::create()
//            ->setHosts(['http://user:@account.west-eu.azr.facetflow.io'])
//            ->build();
//
//        try {
//            var_dump($client->indices()->delete([
//                'index' => 'my_index',
//            ]));
//
//        } catch (\Exception $e) {
//            var_dump($client->transport->getLastConnection()->getLastRequestInfo());
//        }

        $singleHandler = ClientBuilder::singleHandler();
        $multiHandler = ClientBuilder::multiHandler();

        $client = ClientBuilder::create()
            ->setHosts(['http://user:@account.west-eu.azr.facetflow.io'])
            ->setHandler($singleHandler)
            ->build();

        $params = [
            'index' => 'my_index',
            'type'  => 'my_type',
            'id'    => 'my_id',
//            'body'  => [
//                'testField' => 'abc'
//            ]
        ];

        $response = $client->get($params);

        return $response;
        //print_r($client);
    }

    function mapApi()
    {
        $url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=-33.8670,151.1957&radius=500&types=food&name=cruise&key=';
        $key = 'AIzaSyAp_OxlQ8w6fayEK51b-6mpupMi-wbdOwc';
        $client = new Client();

        $request = $client->request('GET', $url . $key);
        $response = json_decode($request->getBody());

        $data = [];

        foreach ($response->results as $key => $val) {
            array_push($data, [
                'name'     => $val->name,
                'category' => $val->types,
                'location' => $val->geometry->location,
                'map-id'   => $val->place_id
            ]);
        }

        return $data;
    }

}
