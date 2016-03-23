<?php namespace App\Domain\Repositories\Map;

use GuzzleHttp\Client;

class MapRepository
{

    var $url = 'https://maps.googleapis.com/maps/api/place';

    var $key = 'AIzaSyAp_OxlQ8w6fayEK51b-6mpupMi-wbdOwc';

    public function __construct()
    {
        //
    }

    /**
     * Get current map url
     *
     * @return string
     */
    public function getMapUrl()
    {
        return $this->url;
    }

    /**
     * Get current map key
     *
     * @return string
     */
    public function getMapKey()
    {
        return $this->key;
    }

    /**
     * Find nearby place
     *
     * @param array $data
     *
     * @return array
     */
    public function nearbySearch(array $data)
    {
        // initial var
        $lat = empty($data['lat']) ? '-6.139998' : $data['lat'];
        $long = empty($data['long']) ? '106.8255004' : $data['long'];
        $radius = empty($data['radius']) ? 500 : $data['radius'];
        $type = empty($data['type']) ? '' : $data['type'];
        $name = empty($data['name']) ? '' : $data['name'];

        $url = $this->url . '/nearbysearch/json?location=' . $lat . ',' . $long . '&radius=' . $radius . '&types=' . $type . '&name=' . $name . '&key=' . $this->key;
        $client = new Client();

        $request = $client->request('GET', $url);
        $response = json_decode($request->getBody());

        $result = [];

        foreach ($response->results as $key => $val) {
            array_push($result, [
                'name'     => $val->name,
                'category' => $val->types,
                'location' => $val->geometry->location,
                'map-id'   => $val->place_id
            ]);
        }

        return $result;
    }

    /**
     * Get detail place
     *
     * @param $placeid
     *
     * @return array
     */
    public function detailPlace($placeid)
    {
        if (empty($placeid)) {
            return [
                'success' => false,
                'message' => 'Please insert placeid!'
            ];
        }

        $url = $this->url . '/details/json?placeid=' . $placeid . '&key=' . $this->key;
        $client = new Client();

        $request = $client->request('GET', $url);

        $data = json_decode($request->getBody());
        $data = $data->result;

        $result = [
            'name'     => $data->name,
            'category' => $data->types,
            'location' => $data->geometry->location,
            'map-id'   => $data->place_id
        ];

        return $result;
    }
}