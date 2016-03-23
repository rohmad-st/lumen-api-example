<?php

namespace App\Http\Controllers\Map;

use App\Domain\Repositories\Map\MapRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * @var MapRepository
     */
    protected $map;

    /**
     * Create a new controller instance.
     * MapController constructor.
     *
     * @param MapRepository $map
     */
    public function __construct(MapRepository $map)
    {
        $this->map = $map;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    function nearbySearch(Request $request)
    {
        return $this->map->nearbySearch($request->all());
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    function findPlace(Request $request)
    {
        return $this->map->findPlace($request->all());
    }

    /**
     * @param $placeid
     *
     * @return array
     */
    function detailPlace($placeid)
    {
        return $this->map->detailPlace($placeid);
    }

}
