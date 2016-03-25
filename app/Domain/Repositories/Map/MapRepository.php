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

    public function getJson()
    {
        $fileJson = file_get_contents(storage_path() . '/json/my_map.json');

        $response = json_decode($fileJson);

        return $response;
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

        // get map api local
        $resultLocal = $this->findPlaceLocal($term);

        if ($resultLocal) {
            return $resultLocal;

        } else {
            // get map api google
            $resultGoogle = $this->findPlaceGoogle($term);

            return $resultGoogle;
        }
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

    public function findPlaceLocal($term)
    {
        $response = $this->getJson();

        // initial term input value
        $data = [];

        foreach ($response as $val) {
            array_push($data, $val->name);
        }

        // search name
        $input = preg_quote($term, '~');
        $result = preg_grep('~' . $input . '~', $data);

        $lastResult = [];
        foreach ($result as $val) {
            array_push($lastResult, [
                'name'     => $val,
                'category' => 0,
                'location' => [
                    'lat'  => 0,
                    'long' => 0
                ],
                'map_id'   => 0,
                'source'   => 0 //0=local; 1=google
            ]);
        }

        return $lastResult;
    }

    /**
     * Find place using auto complete
     *
     * @param $term
     *
     * @return array
     */
    public function findPlaceGoogle($term)
    {
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
                'map_id'   => $placeid,
                'source'   => 1
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