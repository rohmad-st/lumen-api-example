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
                'map_id'   => $val->place_id
            ]);
        }

        return $result;
    }

    /**
     * Find place using auto complete
     *
     * @param array $data
     *
     * @return array
     */
    public function findPlace(array $data)
    {
        // initial var
        $term = empty($data['term']) ? '' : $data['term'];

        $url = $this->url . '/autocomplete/json?input=' . $term . '&types=geocode&key=' . $this->key;
        $client = new Client();

        $request = $client->request('GET', $url);
        $response = json_decode($request->getBody());

        $result = [];

        foreach ($response->predictions as $key => $val) {
            $placeid = $val->place_id;

            // get detail place
            $detailPlace = $this->detailPlace($placeid, []);

            array_push($result, [
                'name'     => $val->description,
                'category' => $val->types,
                'location' => $detailPlace['location'],
                'map_id'   => $placeid
            ]);
        }

        return $result;
    }

    /**
     * Get detail place
     *
     * @param         $placeid
     * @param array   $data
     *
     * @return array
     */
    public function detailPlace($placeid, array  $data)
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
        $is_original = empty($data['is_original']) ? false : $data['is_original'];
        if ($is_original) {
            return $request->getBody();
        }

        $data = json_decode($request->getBody());
        $data = $data->result;

        $result = [
            'name'     => $data->name,
            'category' => $data->types,
            'location' => $data->geometry->location,
            'map_id'   => $data->place_id
        ];

        return $result;
    }
}